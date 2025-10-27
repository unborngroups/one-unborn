<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PincodeLookupController;
use App\Http\Controllers\CompanyController;

// GST API Routes
Route::get('fetch-gst/{gst}', [CompanyController::class, 'fetchGst'])->name('api.fetch-gst');
Route::get('test-gst-api', [CompanyController::class, 'testGstApi'])->name('api.test-gst');

// PAN Company Fetch Route
Route::get('fetch-company-by-pan/{pan}', [CompanyController::class, 'fetchByPan'])->name('api.fetch-company-by-pan');

// Pincode Lookup
Route::post('pincode/lookup', [PincodeLookupController::class, 'lookup'])->name('api.pincode-lookup');
