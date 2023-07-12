<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf + OpenCodeCo
 *
 * @link     https://opencodeco.dev
 * @document https://hyperf.wiki
 * @contact  leo@opencodeco.dev
 * @license  https://github.com/opencodeco/hyperf-metric/blob/main/LICENSE
 */
namespace Hyperf\Metric\Adapter\StatsD;

use Domnikl\Statsd\Connection\UdpSocket;

class Connection extends UdpSocket
{
    protected function isConnected(): bool
    {
        return true;
    }

    protected function writeToSocket(string $message): void
    {
        $errorNumber = 0;
        $errorMessage = '';
        $url = 'udp://' . $this->host;
        $socket = fsockopen($url, $this->port, $errorNumber, $errorMessage);
        fwrite($socket, $message);
        fclose($socket);
        $socket = null;
    }
}
