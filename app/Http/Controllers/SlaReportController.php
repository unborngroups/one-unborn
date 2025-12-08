<?php

namespace App\Http\Controllers;

use App\Models\ClientLink;
use App\Models\SlaReport;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Services\NotificationService;

class SlaReportController extends Controller
{
    // Show SLA history
    public function index($linkId)
    {
        $link = ClientLink::with('client')->findOrFail($linkId);
        $reports = SlaReport::where('client_link_id', $linkId)
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();

        return view('client_portal.sla_reports', compact('reports', 'link'));
    }

    // Monthly SLA Calculation (Cron)
    public function calculate($linkId)
    {
        $link = ClientLink::findOrFail($linkId);

        $now = Carbon::now();
        $month = $now->month;
        $year = $now->year;

        // Quarter dates
        $startOfQuarter = Carbon::parse("{$year}-" . (ceil($month / 3) * 3 - 2) . "-01")->startOfMonth();
        $endOfQuarter   = $startOfQuarter->copy()->addMonths(2)->endOfMonth();

        // Total minutes in quarter
        $totalMinutesInQuarter = $startOfQuarter->diffInMinutes($endOfQuarter);

        // Total downtime
        $totalDowntimeMinutes = DB::table('link_monitoring_data')
            ->where('client_link_id', $linkId)
            ->whereBetween('created_at', [$startOfQuarter, $endOfQuarter])
            ->where('link_up', 0)
            ->count() * 5; // 5 min job interval

        // SLA formula
        $availability = (1 - ($totalDowntimeMinutes / $totalMinutesInQuarter)) * 100;
        $availability = round($availability, 2);

        // Latency + packet loss
        $avgLatency = DB::table('link_monitoring_data')
            ->where('client_link_id', $linkId)
            ->whereBetween('created_at', [$startOfQuarter, $endOfQuarter])
            ->avg('latency_ms');

        $avgPacketLoss = DB::table('link_monitoring_data')
            ->where('client_link_id', $linkId)
            ->whereBetween('created_at', [$startOfQuarter, $endOfQuarter])
            ->avg('packet_loss');

        // Breach check
        $breached = $availability < $link->committed_sla_percentage;

        // Save & get report
        $report = SlaReport::updateOrCreate(
            [
                'client_link_id' => $linkId,
                'month' => $month,
                'year'  => $year,
            ],
            [
                'uptime_percentage' => $availability,
                'downtime_hours'    => round($totalDowntimeMinutes / 60, 2),
                'avg_latency_ms'    => round($avgLatency, 2),
                'avg_packet_loss'   => round($avgPacketLoss, 2),
                'breached'          => $breached,
            ]
        );

        // 🔥 Send SLA breach notification after saving
        if ($breached) {
            NotificationService::sendSlaBreachAlert($link, $report);
        }

        return "SLA calculated successfully for Link #{$linkId}";
    }
}
