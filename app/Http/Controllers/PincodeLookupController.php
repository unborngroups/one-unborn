<?php

namespace App\Http\Controllers;

use App\Models\KnownPincode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class PincodeLookupController extends Controller
{
    public function lookup(Request $request)
    {
        $v = Validator::make($request->all(), [
            'pincode' => ['required', 'digits:6'],
        ]);

        if ($v->fails()) {
            return response()->json(['error' => 'Invalid pincode. Provide a 6-digit number.'], 422);
        }

        $pincode = $request->input('pincode');

        // Check if already stored in DB
        $existing = KnownPincode::where('pincode', $pincode)->first();
        if ($existing) {
            return response()->json([
                'source' => 'db',
                'pincode' => $existing->pincode,
                'state' => $existing->state,
                'district' => $existing->district,
                'post_office' => $existing->post_office,
            ]);
        }

        // Fetch from public API
        $response = Http::withoutVerifying()
    ->timeout(10)
    ->get("https://api.postalpincode.in/pincode/{$pincode}");

        if (! $response->ok()) {
            return response()->json(['error' => 'External API failed'], 502);
        }

        $data = $response->json();

        if (($data[0]['Status'] ?? '') !== 'Success') {
            return response()->json(['error' => 'Pincode not found'], 404);
        }

        $po = $data[0]['PostOffice'][0] ?? null;

        $record = KnownPincode::create([
            'pincode' => $pincode,
            'state' => $po['State'] ?? null,
            'district' => $po['District'] ?? null,
            'post_office' => $po['Name'] ?? null,
        ]);

        return response()->json([
            'source' => 'api',
            'pincode' => $record->pincode,
            'state' => $record->state,
            'district' => $record->district,
            'post_office' => $record->post_office,
        ]);
    }

}
