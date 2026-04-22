<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\CompanySetting;
use App\Models\EmailLog;
use App\Models\Renewal;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class RecurringInvoiceController extends Controller
{
    public function index(Request $request)
    {
        $selectedClient = null;

        $recurringInvoices = Renewal::with(['deliverable.feasibility.client'])
            ->when($request->filled('client_id'), function ($query) use ($request, &$selectedClient) {
                $clientId = (int) $request->client_id;
                $selectedClient = Client::find($clientId);

                $query->whereHas('deliverable.feasibility', function ($q) use ($clientId) {
                    $q->where('client_id', $clientId);
                });
            })
            ->latest()
            ->get();

        $invoiceRows = $recurringInvoices->map(function (Renewal $renewal) {
            return $this->buildFormulaRow($renewal);
        });

        $summary = [
            'total_formula_amount' => round($invoiceRows->sum('formula_amount'), 2),
            'total_arc_component' => round($invoiceRows->sum('arc_component'), 2),
            'total_static_component' => round($invoiceRows->sum('static_component'), 2),
        ];

        return view('finance.sales.recurring_invoice.index', compact('recurringInvoices', 'selectedClient', 'invoiceRows', 'summary'));
    }

    private function buildFormulaRow(Renewal $renewal): array
    {
        $deliverable = $renewal->deliverable;
        $startDate = $renewal->date_of_renewal ? Carbon::parse($renewal->date_of_renewal) : null;
        $endDate = $renewal->new_expiry_date ? Carbon::parse($renewal->new_expiry_date) : null;

        $billableDays = ($startDate && $endDate && $endDate->gte($startDate))
            ? $startDate->diffInDays($endDate) + 1
            : 0;

        $months = max(1, (int) ($renewal->renewal_months ?? 1));

        // Prefer deliverable values; fallback to purchase order values when available.
        $annualArc = (float) ($deliverable->arc_cost ?? 0);
        $annualStatic = (float) ($deliverable->static_ip_cost ?? 0);

        if ($annualArc <= 0 && isset($deliverable->purchase_order) && $deliverable->purchase_order) {
            $annualArc = (float) (($deliverable->purchase_order->arc_per_link ?? 0) * 12);
        }

        if ($annualStatic <= 0 && isset($deliverable->purchase_order) && $deliverable->purchase_order) {
            $annualStatic = (float) (($deliverable->purchase_order->static_ip_cost_per_link ?? 0) * 12);
        }

        $annualTotal = $annualArc + $annualStatic;

        // Spreadsheet-aligned quarter rule: quarter amount / quarter days for 3-month renewals.
        if ($months === 3 && $startDate) {
            $quarterDays = $this->quarterDays($startDate);
            $periodAmount = $annualTotal / 4;
            $dayRate = $quarterDays > 0 ? $periodAmount / $quarterDays : 0;
        } else {
            // General recurring formula for other cycles.
            $periodDays = $months * 30;
            $periodAmount = $annualTotal * ($months / 12);
            $dayRate = $periodDays > 0 ? $periodAmount / $periodDays : 0;
        }

        $formulaAmount = round($dayRate * $billableDays, 2);

        $arcComponent = round(($annualArc > 0 ? $formulaAmount * ($annualArc / max($annualTotal, 1)) : 0), 2);
        $staticComponent = round($formulaAmount - $arcComponent, 2);

        return [
            'renewal' => $renewal,
            'client_name' => $deliverable->feasibility->client->client_name ?? '-',
            'circuit_id' => $renewal->circuit_id ?? ($deliverable->circuit_id ?? '-'),
            'start_date' => $startDate?->format('d-m-Y') ?? '-',
            'end_date' => $endDate?->format('d-m-Y') ?? '-',
            'renewal_months' => $months,
            'billable_days' => $billableDays,
            'annual_arc' => round($annualArc, 2),
            'annual_static' => round($annualStatic, 2),
            'annual_total' => round($annualTotal, 2),
            'day_rate' => round($dayRate, 6),
            'formula_amount' => $formulaAmount,
            'arc_component' => $arcComponent,
            'static_component' => $staticComponent,
        ];
    }

    private function quarterDays(Carbon $date): int
    {
        $month = (int) $date->month;

        if (in_array($month, [4, 5, 6], true)) {
            return 91; // AMJ
        }

        if (in_array($month, [7, 8, 9], true)) {
            return 92; // JAS
        }

        if (in_array($month, [10, 11, 12], true)) {
            return 92; // OND
        }

        return 90; // JFM
    }

    public function sendEmail(string $id)
    {
        $renewal = Renewal::with(['deliverable.feasibility.client', 'deliverable.feasibility.company'])
            ->findOrFail($id);

        $deliverable = $renewal->deliverable;
        $client = $deliverable->feasibility->client ?? null;
        $company = $deliverable->feasibility->company ?? null;

        $to = $this->normalizeEmails($client->invoice_email ?? null);
        $cc = $this->normalizeEmails($client->invoice_cc ?? null);
        $subject = 'Recurring Invoice - ' . ($renewal->circuit_id ?? ('Renewal #' . $renewal->id));

        $emailLog = EmailLog::create([
            'sender' => null,
            'subject' => $subject,
            'body' => json_encode([
                'renewal_id' => $renewal->id,
                'circuit_id' => $renewal->circuit_id,
                'to' => $to,
                'cc' => $cc,
                'status' => 'pending',
            ]),
            'status' => 'pending',
        ]);

        if (empty($to)) {
            $emailLog->update([
                'status' => 'failed',
                'error_message' => 'Client invoice_email is empty.',
            ]);

            return back()->with('error', 'Client invoice email not found. Please update Client Master invoice_email.');
        }

        try {
            $companySetting = $this->resolveInvoiceCompanySetting($company?->id);
            $this->applyInvoiceMailConfig($companySetting);

            $fromAddress = trim((string) (
                $companySetting?->sales_invoice_mail_from_address
                ?: $companySetting?->invoice_mail_from_address
                ?: config('mail.from.address')
            ));
            $fromName = trim((string) (
                $companySetting?->sales_invoice_mail_from_name
                ?: $companySetting?->invoice_mail_from_name
                ?: config('mail.from.name', 'Unborn')
            ));

            $mailBody = view('emails.recurring_invoice', [
                'renewal' => $renewal,
                'deliverable' => $deliverable,
                'client' => $client,
                'company' => $company,
                'formula' => $this->buildFormulaRow($renewal),
            ])->render();

            Mail::send([], [], function ($message) use ($to, $cc, $subject, $mailBody, $fromAddress, $fromName) {
                $message->to($to)->subject($subject);
                if (!empty($cc)) {
                    $message->cc($cc);
                }
                if (!empty($fromAddress)) {
                    $message->from($fromAddress, $fromName ?: $fromAddress);
                }
                $message->html($mailBody);
            });

            $emailLog->update([
                'sender' => $fromAddress ?: null,
                'status' => 'processed',
                'error_message' => null,
                'body' => json_encode([
                    'renewal_id' => $renewal->id,
                    'circuit_id' => $renewal->circuit_id,
                    'to' => $to,
                    'cc' => $cc,
                    'status' => 'processed',
                ]),
            ]);

            return back()->with('success', 'Recurring invoice email sent successfully.');
        } catch (\Throwable $e) {
            Log::error('Recurring invoice email send failed', [
                'renewal_id' => $renewal->id,
                'message' => $e->getMessage(),
            ]);

            $emailLog->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            return back()->with('error', 'Failed to send recurring invoice email: ' . $e->getMessage());
        }
    }

    private function resolveInvoiceCompanySetting(?int $companyId): ?CompanySetting
    {
        if ($companyId) {
            $byCompany = CompanySetting::where('company_id', $companyId)->first();
            if ($byCompany) {
                return $byCompany;
            }
        }

        return CompanySetting::query()->orderByDesc('is_default')->orderBy('id')->first();
    }

    private function applyInvoiceMailConfig(?CompanySetting $setting): void
    {
        if (!$setting) {
            return;
        }

        $host = $setting->sales_invoice_mail_host ?: $setting->invoice_mail_host ?: $setting->mail_host;
        $port = (int) ($setting->sales_invoice_mail_port ?: $setting->invoice_mail_port ?: $setting->mail_port ?: 587);
        $username = $setting->sales_invoice_mail_username ?: $setting->invoice_mail_username ?: $setting->mail_username;
        $password = $setting->sales_invoice_mail_password ?: $setting->invoice_mail_password ?: $setting->mail_password;
        $encryption = strtolower((string) ($setting->sales_invoice_mail_encryption ?: $setting->invoice_mail_encryption ?: $setting->mail_encryption ?: 'tls'));
        $fromAddress = $setting->sales_invoice_mail_from_address ?: $setting->invoice_mail_from_address ?: $setting->mail_from_address;
        $fromName = $setting->sales_invoice_mail_from_name ?: $setting->invoice_mail_from_name ?: $setting->mail_from_name;

        if (empty($host) || empty($username) || empty($password) || empty($fromAddress)) {
            return;
        }

        Config::set('mail.default', 'smtp');
        Config::set('mail.mailers.smtp.transport', 'smtp');
        Config::set('mail.mailers.smtp.host', $host);
        Config::set('mail.mailers.smtp.port', $port);
        Config::set('mail.mailers.smtp.encryption', $encryption);
        Config::set('mail.mailers.smtp.username', $username);
        Config::set('mail.mailers.smtp.password', $password);
        Config::set('mail.from.address', $fromAddress);
        Config::set('mail.from.name', $fromName ?: 'Unborn');

        Mail::purge();
    }

    private function normalizeEmails($emails): array
    {
        if (!$emails) {
            return [];
        }

        if (is_array($emails)) {
            return array_values(array_filter(array_map('trim', $emails)));
        }

        return array_values(array_filter(array_map('trim', preg_split('/[,;]/', (string) $emails))));
    }
}
