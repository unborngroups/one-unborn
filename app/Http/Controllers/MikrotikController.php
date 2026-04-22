<?php

namespace App\Http\Controllers;

use App\Models\ClientLink;
use App\Services\MikrotikService;

class MikrotikController extends Controller
{
    /** 🔥 LIVE STATUS PAGE */
    public function showStatus($linkId)
    {
        $link = ClientLink::with('router')->findOrFail($linkId);
        return view('client_portal.status', compact('link'));
    }

    /** 🔥 AJAX CALL FOR REAL-TIME TRAFFIC */
    public function getLiveTraffic($linkId)
    {
        $link = ClientLink::with('router')->findOrFail($linkId);

        $traffic = MikrotikService::getTraffic($link->router, $link->interface_name);
        $ping    = MikrotikService::ping($link->router);

        return response()->json([
            'rx'          => $traffic['rx'],
            'tx'          => $traffic['tx'],
            'latency'     => $ping['latency'],
            'packet_loss' => $ping['packet_loss'],
        ]);
    }
}
