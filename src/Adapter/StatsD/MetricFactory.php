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

use Domnikl\Statsd\Client;
use Domnikl\Statsd\Connection;
use Hyperf\Contract\ConfigInterface;
use Hyperf\Coordinator\Constants;
use Hyperf\Coordinator\CoordinatorManager;
use Hyperf\Metric\Contract\CounterInterface;
use Hyperf\Metric\Contract\GaugeInterface;
use Hyperf\Metric\Contract\HistogramInterface;
use Hyperf\Metric\Contract\MetricFactoryInterface;

use function Hyperf\Support\env;
use function Hyperf\Support\make;

class MetricFactory implements MetricFactoryInterface
{
    private Client $client;

    public function __construct(private ConfigInterface $config)
    {
        $this->client = make(Client::class, [
            'connection' => $this->getConnection(),
            'namespace' => $this->getNamespace(),
            'sampleRateAllMetrics' => $this->getSampleRate(),
        ]);
    }

    public function makeCounter(string $name, ?array $labelNames = []): CounterInterface
    {
        return (new Counter(
            $this->client,
            $name,
            $this->getSampleRate(),
            array_merge(['service.name', 'host.name'], $labelNames),
        ))->with($this->getNamespace(), $this->getHostname());
    }

    public function makeGauge(string $name, ?array $labelNames = []): GaugeInterface
    {
        return (new Gauge(
            $this->client,
            $name,
            $this->getSampleRate(),
            array_merge(['service.name', 'host.name'], $labelNames),
        ))->with($this->getNamespace(), $this->getHostname());
    }

    public function makeHistogram(string $name, ?array $labelNames = []): HistogramInterface
    {
        return (new Histogram(
            $this->client,
            $name,
            $this->getSampleRate(),
            array_merge(['service.name', 'host.name'], $labelNames),
        ))->with($this->getNamespace(), $this->getHostname());
    }

    public function handle(): void
    {
        $name = $this->config->get('metric.default');
        $interval = (float) $this->config->get("metric.metric.{$name}.push_interval", 5);
        $batchEnabled = $this->config->get("metric.metric.{$name}.enable_batch") == true;
        // Block handle from returning.
        if ($batchEnabled) {
            while (true) {
                $this->client->startBatch();
                $workerExited = CoordinatorManager::until(Constants::WORKER_EXIT)->yield($interval);
                $this->client->endBatch();
                if ($workerExited) {
                    break;
                }
            }
        } else {
            CoordinatorManager::until(Constants::WORKER_EXIT)->yield();
        }
    }

    protected function getConnection(): Connection
    {
        $name = $this->config->get('metric.default');
        $host = $this->config->get("metric.metric.{$name}.udp_host");
        $port = $this->config->get("metric.metric.{$name}.udp_port");
        $timeout = $this->config->get("metric.metric.{$name}.timeout");
        $persistent = $this->config->get("metric.metric.{$name}.persistent", false);
        return make(Connection::class, [
            'host' => $host,
            'port' => (int) $port,
            'timeout' => $timeout,
            'persistent' => $persistent,
        ]);
    }

    protected function getNamespace(): string
    {
        $name = $this->config->get('metric.default');
        return $this->config->get("metric.metric.{$name}.namespace");
    }

    protected function getSampleRate(): float
    {
        $name = $this->config->get('metric.default');
        return $this->config->get("metric.metric.{$name}.sample_rate", 1.0);
    }

    protected function getHostname(): string
    {
        return $this->config->get('hostname', env('HOSTNAME'));
    }
}
