<?php

namespace App\Jobs;

use App\Models\ClientLink;
use App\Models\MikrotikRouter;
use App\Models\LinkMonitoringData;
use App\Services\MikrotikService;
use App\Services\NotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Bus\Dispatchable;

class CollectLinkMetrics implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle()
    {
        $links = ClientLink::with('router')->get();

        foreach ($links as $link) {
            $router = $link->router;
            if (!$router) {
                continue; // no router mapped
            }

            // 🔹 Ping metrics from MikroTik
            $ping = MikrotikService::ping($router, $link->destination_ip);
            
            $latency = $ping['latency'] ?? null;
            $packetLoss = $ping['packet_loss'] ?? 0;
            $linkUp = $packetLoss == 100 ? 0 : 1;

            // 🔹 Save metrics
            LinkMonitoringData::create([
                'client_link_id' => $link->id,
                'latency_ms'     => $latency ?? 0,
                'packet_loss'    => $packetLoss,
                'link_up'        => $linkUp,
            ]);

            // 🔥 Alert conditions
            if (!$linkUp) {
                NotificationService::sendLinkDownAlert($link);
                continue;
            }

            if ($latency > $link->latency_threshold) {
                NotificationService::sendHighLatencyAlert($link, $latency);
            }

            if ($packetLoss > $link->packet_loss_threshold) {
                NotificationService::sendHighPacketLossAlert($link, $packetLoss);
            }
        }
    }
}
