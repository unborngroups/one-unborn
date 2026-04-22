<?php

namespace App\Services;

use RouterOS\Client;
use RouterOS\Query;

class MikrotikService
{
    public static function connect($router)
    {
        return new Client([
            'host' => $router->ip_address,
            'user' => $router->api_username,
            'pass' => $router->api_password,
            'port' => $router->api_port ?? 8728,
            'timeout' => 10,
        ]);
    }

    public static function getTraffic($router, $interface)
    {
        $client = self::connect($router);
        $query = new Query('/interface/monitor-traffic');
        $query->equal('interface', $interface)->equal('once', 'yes');
        $response = $client->query($query)->read();

        return [
            'rx' => $response[0]['rx-bits-per-second'] ?? 0,
            'tx' => $response[0]['tx-bits-per-second'] ?? 0,
        ];
    }

    public static function ping($router, $target = '8.8.8.8')
    {
        $client = self::connect($router);
        $query = new Query('/ping');
        $query->equal('address', $target)->equal('count', 5);
        $response = $client->query($query)->read();

        return [
            'latency' => $response[0]['time'] ?? null,
            'packet_loss' => $response[0]['packet-loss'] ?? null,
        ];
    }
}
