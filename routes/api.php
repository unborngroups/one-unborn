<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\PincodeLookupController;
use App\Http\Controllers\Api\InboundEmailController;
use App\Http\Controllers\Finance\EmailInvoiceController;
use App\Models\Client;

// GST API Routes
Route::get('fetch-gst/{gst}', [CompanyController::class, 'fetchGst'])->name('api.fetch-gst');
Route::get('test-gst-api', [CompanyController::class, 'testGstApi'])->name('api.test-gst');


Route::get('/gst/fetch/{pan}/{state}', [CompanyController::class, 'fetchGST']);

// PAN Company Fetch Route
Route::get('fetch-company-by-pan/{pan}', [CompanyController::class, 'fetchByPan'])->name('api.fetch-company-by-pan');

// PAN Verification Route
Route::get('/verify-pan', [ClientController::class, 'verifyPan']);

// Pincode Lookup Route
Route::post('/pincode/lookup', [PincodeLookupController::class, 'lookup'])->name('api.pincode.lookup');

// client send password route (allow OPTIONS for preflight)
Route::match(['post', 'options'], '/client/send-password', [ClientController::class, 'sendPassword']);

// Email inbound webhook (with duplicate prevention)
Route::post('/email/inbound', [InboundEmailController::class, 'receive'])
    ->middleware(\App\Http\Middleware\PreventDuplicateInvoiceProcessing::class)
    ->name('api.email.inbound');

// Company-specific email inbound webhook (auto-detects company from recipient email)
Route::post('/companies/{companyId}/email/inbound', [InboundEmailController::class, 'receive'])
    ->middleware(\App\Http\Middleware\PreventDuplicateInvoiceProcessing::class)
    ->name('api.company.email.inbound');

// AI Gmail invoice processing endpoints (aliases to existing invoice email parser flow)
Route::post('/ai/gmail/invoice/create', [EmailInvoiceController::class, 'receiveEmailWebhook'])
    ->name('ai.gmail.invoice.create');

Route::post('/ai/mail/invoice/process', [EmailInvoiceController::class, 'receiveEmailWebhook'])
    ->name('api.ai.mail.invoice.process');

// API route to get head office details in branch client creation/editing form
Route::get('/clients/head-office/{id}', function ($id) {
    return Client::select(
        'client_name',
        'short_name',
        'business_display_name'
    )->where('office_type', 'head')->findOrFail($id);
});

// Purchase Invoice Verification Workflow API Routes (with auth and role-based access)
Route::prefix('purchase-invoices')
    ->middleware([
        'auth:api',
        \App\Http\Middleware\InvoiceAuthMiddleware::class
    ])
    ->group(function () {
        Route::get('status/{status}', [App\Http\Controllers\Api\PurchaseInvoiceController::class, 'indexByStatus'])->name('api.purchase-invoices.by-status');
        Route::get('{id}', [App\Http\Controllers\Api\PurchaseInvoiceController::class, 'show'])->name('api.purchase-invoices.show');
        Route::post('{id}/verify', [App\Http\Controllers\Api\PurchaseInvoiceController::class, 'verify'])->name('api.purchase-invoices.verify');
        Route::post('{id}/approve', [App\Http\Controllers\Api\PurchaseInvoiceController::class, 'approve'])->name('api.purchase-invoices.approve');
        Route::post('{id}/mark-paid', [App\Http\Controllers\Api\PurchaseInvoiceController::class, 'markPaid'])->name('api.purchase-invoices.mark-paid');
    });