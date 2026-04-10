<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Renewal;
use Carbon\Carbon;
use Illuminate\Http\Request;

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
}
