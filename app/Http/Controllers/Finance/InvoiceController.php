<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\Deliverables;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Mail;

class InvoiceController extends Controller
{

    public function index()
    {
        $invoices = Invoice::latest()->get();
        return view('finance.invoices.index', compact('invoices'));
    }

    public function create(Request $request)
    {

        $deliverable = Deliverables::with([
            'feasibility.company',
            'feasibility.client',
            'purchase_order'
        ])->findOrFail($request->deliverable_id);

        return view('finance.invoices.create', compact('deliverable'));
    }

    public function store(Request $request)
    {

                $deliverable = Deliverables::with(['feasibility.company', 'feasibility.client'])->findOrFail($request->deliverable_id);

                $invoice = new Invoice();
                $invoice->deliverable_id = $deliverable->id;
                $invoice->invoice_no = $this->getNextInvoiceNumber(
                        $deliverable->feasibility->company->company_name ?? '',
                        $deliverable->feasibility->client->client_name ?? ''
                );
                $invoice->invoice_date = $request->invoice_date;
                $invoice->due_date = $request->due_date;
                // Set required customer_name field
                $invoice->customer_name = $deliverable->feasibility->client->client_name ?? '';
                $invoice->save();

        // link delivery
        $deliverable->invoice_id = $invoice->id;
        $deliverable->save();

        return redirect()->route('finance.invoices.index')
                 ->with('success','Invoice Created');
    }

    public function view($id)
    {
        $invoice = Invoice::with([
            'items',
            'company',
            'client',
            'deliverable.feasibility.company',
            'deliverable.feasibility.client',
            'deliverable.purchaseOrder',
         
        ])->findOrFail($id);
        $company = $invoice->company;
        $client = $invoice->client;
        $deliverables = $invoice->deliverable;
        $feasibility = $deliverables->feasibility ?? null;
        return view('finance.invoices.view', compact('invoice', 'company', 'client', 'deliverables', 'feasibility'));
    }

    public function edit($id)
    {
        $invoice = Invoice::findOrFail($id);
        return view('finance.invoices.edit', compact('invoice'));
    }

    public function update(Request $request, $id)
    {
        $invoice = Invoice::findOrFail($id);

        $invoice->invoice_date = $request->invoice_date;
        $invoice->due_date = $request->due_date;
        $invoice->sub_total = $request->amount;
        $invoice->grand_total = $request->amount;
        $invoice->save();

        return redirect()->route('invoices.index')
                 ->with('success','Invoice Updated');
    }

    public function pdf($id)
    {
        $invoice = Invoice::with([
            'items',
            'company',
            'client',
            'deliverable.feasibility.company',
            'deliverable.feasibility.client',
            'deliverable.purchaseOrder',
        ])->findOrFail($id);
        $company = $invoice->company;
        $client = $invoice->client;
        $deliverables = $invoice->deliverable;
        $feasibility = $deliverables->feasibility ?? null;
        $pdf = PDF::loadView('finance.invoices.view', compact('invoice', 'company', 'client', 'deliverables', 'feasibility'));
        return $pdf->download('Invoice_' . $invoice->invoice_no . '.pdf');
    }

    public function sendEmail($id)
    {
        $invoice = Invoice::with(['items', 'company', 'client', 'deliverable'])->findOrFail($id);
        $company = $invoice->company;
        $client = $invoice->client;
        $pdf = PDF::loadView('finance.invoices.view', compact('invoice', 'company', 'client'));

        $to = $client->invoice_email ?? $client->email;
        $cc = $client->invoice_cc ?? null;

        Mail::send('emails.invoice', compact('invoice', 'company', 'client'), function($message) use ($invoice, $pdf, $to, $cc) {
            $message->to($to)
                ->subject('Invoice - ' . $invoice->invoice_no)
                ->attachData($pdf->output(), 'Invoice_' . $invoice->invoice_no . '.pdf');
            if ($cc) {
                $message->cc($cc);
            }
        });

        return back()->with('success', 'Invoice Sent Successfully');
    }

    private function getCompanyCode($name)
    {
        return strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $name), 0, 3));
    }

    private function getNextInvoiceNumber($ourCompany, $clientCompany)
    {
        $ourCode = $this->getCompanyCode($ourCompany);
        $clientCode = $this->getCompanyCode($clientCompany);

        $prefix = $ourCode . '-' . $clientCode . '-';

        $lastInvoice = Invoice::where('invoice_no','like',$prefix.'%')
                        ->orderBy('id','desc')
                        ->first();

        if ($lastInvoice) {
            $lastNumber = intval(substr($lastInvoice->invoice_no,-6));
            $nextNumber = str_pad($lastNumber + 1,6,'0',STR_PAD_LEFT);
        } else {
            $nextNumber = '000001';
        }

        return $prefix.$nextNumber;
    }

       public function destroy($id)
    {
        $invoice = Invoice::findOrFail($id);
        // Unlink from deliverable if linked
        if ($invoice->deliverable_id) {
            $deliverable = Deliverables::find($invoice->deliverable_id);
            if ($deliverable) {
                $deliverable->invoice_id = null;
                $deliverable->save();
            }
        }
        $invoice->delete();
        return redirect()->route('finance.invoices.index')->with('success', 'Invoice deleted successfully');
    }
    
}
