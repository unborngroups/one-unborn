<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\ClientLink;
use App\Models\SlaReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ClientPortalController extends Controller
{
    public function loginPage()
    {
        return view('client_portal.login');
    }

public function login(Request $request)
{
    $client = Client::where('user_name', $request->user_name)
        ->where('portal_active', 1)
        ->first();

    if ($client && Hash::check($request->portal_password, $client->portal_password)) {

        Auth::guard('client')->login($client);

        $client->update(['portal_last_login' => now()]);

        return redirect()->route('client.dashboard');
    }

    return back()->with('error', 'Invalid Username or Password');
}



    public function dashboard()
    {
        $client = Auth::guard('client')->user();
        $links = ClientLink::where('client_id', $client->id)
            ->with('router')
            ->get();

        return view('client_portal.dashboard', compact('client', 'links'));
    }

    /** 🔥  FIX FOR "Undefined method links()" */
    public function links()
    {
        $client = Auth::guard('client')->user();
        $links = ClientLink::where('client_id', $client->id)
            ->with('router')
            ->get();

        $link = $links->first();
        return view('client_portal.link_details', compact('client', 'links', 'link'));
    }

   public function slaReports($id)
{
    $client = Auth::guard('client')->user();
    $link = ClientLink::where('client_id', $client->id)->where('id', $id)->firstOrFail();

    $slaReports = SLAReport::where('client_link_id', $id)
        ->orderBy('year')
        ->orderBy('month')
        ->get();

    // Graph values
    $labels = $slaReports->map(fn($r) => $r->month . '/' . $r->year);
    $uptime = $slaReports->map(fn($r) => $r->uptime_percentage);
    $latency = $slaReports->map(fn($r) => $r->avg_latency_ms);
    $packetLoss = $slaReports->map(fn($r) => $r->avg_packet_loss);

    return view('client_portal.sla_reports', compact(
        'link', 'slaReports', 'labels', 'uptime', 'latency', 'packetLoss'
    ));
}



    public function linkDetails($id)
    {
        $client = Auth::guard('client')->user();
        $link = ClientLink::where('client_id', $client->id)
            ->where('id', $id)
            ->with('router')
            ->firstOrFail();

        $slaReports = SlaReport::where('client_link_id', $id)
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->take(12)
            ->get();

        return view('client_portal.link_details', compact('client', 'link', 'slaReports'));
    }
    public function liveTraffic($id)
{
    $link = ClientLink::find($id);

    if (!$link) {
        return response()->json(['error' => true], 404);
    }

    // For now just return a dummy response
    return response()->json([
        'link_up' => true,   // or false | later you can check via API or SNMP
    ]);
}


    public function logout()
    {
        Auth::guard('client')->logout();
        return redirect()->route('client.login');
    }
}
