<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\ClientLink;
use App\Models\SlaReport;

class SLAService
{
    /**
     * Main entry: Calculate monthly SLA for a link
     */
    public function calculateMonthlySLA(int $linkId, string $month): array
    {
        $link = ClientLink::findOrFail($linkId);

        $start = Carbon::parse($month)->startOfMonth();
        $end   = Carbon::parse($month)->endOfMonth();

        // Total minutes in month
        $totalMinutes = $start->diffInMinutes($end);

        // Downtime calculation (continuous block logic)
        $downtimeMinutes = $this->calculateDowntimeMinutesFromMonitoring(
            $linkId,
            $start,
            $end
        );

        // Availability %
        $availability = 0;
        if ($totalMinutes > 0) {
            $availability = round(
                (($totalMinutes - $downtimeMinutes) / $totalMinutes) * 100,
                3
            );
        }

        // Latency & packet loss
        $latencyPacketLoss = $this->latencyPacketLossSummary(
            $linkId,
            $start,
            $end
        );

        // SLA target & breach
        $slaTarget = $link->committed_sla_percentage;
        $breached  = $availability < $slaTarget;

        // Persist monthly SLA report
        $report = SlaReport::updateOrCreate(
            [
                'client_link_id' => $linkId,
                'month'          => $start->month,
                'year'           => $start->year,
            ],
            [
                'uptime_percentage' => $availability,
                'downtime_hours'    => round($downtimeMinutes / 60, 2),
                'avg_latency_ms'    => $latencyPacketLoss['avg_latency'],
                'avg_packet_loss'   => $latencyPacketLoss['packet_loss'],
                'breached'          => $breached,
            ]
        );

        return [
            'link_id'          => $linkId,
            'period'           => $start->format('M Y'),
            'sla_target'       => $slaTarget,
            'availability'     => $availability,
            'downtime_minutes' => $downtimeMinutes,
            'avg_latency'      => $latencyPacketLoss['avg_latency'],
            'packet_loss'      => $latencyPacketLoss['packet_loss'],
            'status'           => $breached ? 'FAIL' : 'PASS',
            'report_id'        => $report->id,
        ];
    }

    /**
     * Calculate downtime minutes using continuous DOWN blocks
     * (enterprise-grade logic)
     */
    protected function calculateDowntimeMinutesFromMonitoring(
        int $linkId,
        Carbon $start,
        Carbon $end
    ): int {

        $records = DB::table('link_monitoring_data')
            ->where('client_link_id', $linkId)
            ->whereBetween('created_at', [$start, $end])
            ->orderBy('created_at')
            ->get(['link_up', 'created_at']);

        $totalDowntimeMinutes = 0;
        $downtimeStart = null;

        foreach ($records as $row) {

            if ($row->link_up == 0 && $downtimeStart === null) {
                // DOWN started
                $downtimeStart = Carbon::parse($row->created_at);
            }

            if ($row->link_up == 1 && $downtimeStart !== null) {
                // DOWN ended
                $downtimeEnd = Carbon::parse($row->created_at);

                $totalDowntimeMinutes += $downtimeStart->diffInMinutes($downtimeEnd);
                $downtimeStart = null;
            }
        }

        // Still DOWN at end of month
        if ($downtimeStart !== null) {
            $totalDowntimeMinutes += $downtimeStart->diffInMinutes($end);
        }

        return $totalDowntimeMinutes;
    }

    /**
     * Latency & packet loss summary
     */
    protected function latencyPacketLossSummary(
        int $linkId,
        Carbon $start,
        Carbon $end
    ): array {

        $query = DB::table('link_monitoring_data')
            ->where('client_link_id', $linkId)
            ->whereBetween('created_at', [$start, $end]);

        return [
            'avg_latency' => round((float) $query->avg('latency_ms'), 2),
            'packet_loss' => round((float) $query->avg('packet_loss'), 2),
        ];
    }
}
