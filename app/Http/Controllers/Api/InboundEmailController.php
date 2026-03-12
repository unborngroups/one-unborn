<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EmailLog;
use App\Jobs\ProcessInvoiceJob;
use Illuminate\Support\Facades\Storage;

class InboundEmailController extends Controller
{
    public function receive(Request $request)
    {
        try {

            $attachment = $request->file('attachment');

            if (!$attachment || $attachment->getClientOriginalExtension() !== 'pdf') {
                return response()->json(['message' => 'Invalid file'], 400);
            }

            $path = $attachment->store('invoices');

            $email = EmailLog::create([
                'sender' => $request->input('sender'),
                'subject' => $request->input('subject'),
                'body' => $request->input('body'),
                'attachment_path' => $path,
                'status' => 'pending'
            ]);

            ProcessInvoiceJob::dispatch($email->id);

            return response()->json(['message' => 'Email received']);

        } catch (\Exception $e) {

            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
