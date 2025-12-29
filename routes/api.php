<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\PincodeLookupController;
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

// API route to get head office details in branch client creation/editing form
Route::get('/clients/head-office/{id}', function ($id) {
    return Client::select(
        'client_name',
        'short_name',
        'business_display_name'
    )->where('office_type', 'head')->findOrFail($id);
});