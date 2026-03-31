<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Models\EmailLog;

class PreventDuplicateInvoiceProcessing
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Extract email identifier from request
        $emailIdentifier = $this->getEmailIdentifier($request);

        if (!$emailIdentifier) {
            return $next($request);
        }

        // Check if email is already processing
        $lockKey = "invoice:processing:{$emailIdentifier}";
        $processedKey = "invoice:processed:{$emailIdentifier}";

        // Check if already processed
        if (Cache::has($processedKey)) {
            Log::warning("Duplicate invoice processing attempt: {$emailIdentifier}");
            return response()->json([
                'success' => false,
                'message' => 'Invoice from this email has already been processed.',
                'error_code' => 'DUPLICATE_PROCESSING'
            ], 409);
        }

        // Try to acquire processing lock (60 second timeout)
        if (Cache::has($lockKey)) {
            Log::warning("Concurrent invoice processing detected: {$emailIdentifier}");
            return response()->json([
                'success' => false,
                'message' => 'This invoice is currently being processed. Please try again later.',
                'error_code' => 'PROCESSING_IN_PROGRESS'
            ], 429);
        }

        // Acquire lock
        Cache::put($lockKey, true, 60);

        try {
            $response = $next($request);

            // If processing succeeded, mark as processed (24 hour expiry)
            if ($response->status() < 400) {
                Cache::put($processedKey, true, 86400);
                Log::info("Invoice processing completed: {$emailIdentifier}");
            }

            return $response;
        } finally {
            // Always release lock
            Cache::forget($lockKey);
        }
    }

    /**
     * Extract email identifier from request.
     * Try multiple sources: message_id, email_id, attachment_path
     */
    private function getEmailIdentifier(Request $request)
    {
        // From Mailgun/SES webhook
        if ($request->has('message_id')) {
            return trim($request->input('message_id'));
        }

        // From form data
        if ($request->has('email_id')) {
            return 'email:' . $request->input('email_id');
        }

        // From file hash (for file-based uploads)
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            return 'file:' . hash_file('sha256', $file->getPathname());
        }

        // From attachment path
        if ($request->has('attachment_path')) {
            return 'path:' . hash('sha256', $request->input('attachment_path'));
        }

        return null;
    }
}
