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
namespace Hyperf\Metric;

use Domnikl\Statsd\Connection;
use Hyperf\Metric\Adapter\StatsD\Connection as StatsDConnection;
use Hyperf\Metric\Aspect\CounterAnnotationAspect;
use Hyperf\Metric\Aspect\HistogramAnnotationAspect;
use Hyperf\Metric\Aspect\HttpClientMetricAspect;
use Hyperf\Metric\Aspect\MongoCollectionMetricAspect;
use Hyperf\Metric\Aspect\RedisMetricAspect;
use Hyperf\Metric\Contract\MetricFactoryInterface;
use Hyperf\Metric\Contract\SqlSanitizerInterface;
use Hyperf\Metric\Listener\DbQueryExecutedMetricListener;
use Hyperf\Metric\Adapter\RemoteProxy\MetricCollectorFactory;
use Hyperf\Metric\Contract\MetricCollectorInterface;
use Hyperf\Metric\Listener\MetricBufferWatcher;
use Hyperf\Metric\Listener\OnBeforeHandle;
use Hyperf\Metric\Listener\OnCoroutineServerStart;
use Hyperf\Metric\Listener\OnMetricFactoryReady;
use Hyperf\Metric\Listener\OnPipeMessage;
use Hyperf\Metric\Listener\OnWorkerStart;
use Hyperf\Metric\Middleware\MetricMiddleware;
use Hyperf\Metric\Process\MetricProcess;
use Hyperf\Metric\Support\SqlSanitizer;
use InfluxDB\Driver\DriverInterface;
use InfluxDB\Driver\Guzzle;
use Prometheus\Storage\Adapter;
use Prometheus\Storage\InMemory;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => [
                MetricFactoryInterface::class => MetricFactoryPicker::class,
                Adapter::class => InMemory::class,
                Connection::class => StatsDConnection::class,
                DriverInterface::class => Guzzle::class,
                SqlSanitizerInterface::class => SqlSanitizer::class,
                MetricCollectorInterface::class => MetricCollectorFactory::class,
            ],
            'aspects' => [
                CounterAnnotationAspect::class,
                HistogramAnnotationAspect::class,
                HttpClientMetricAspect::class,
                MongoCollectionMetricAspect::class,
                RedisMetricAspect::class,
            ],
            'publish' => [
                [
                    'id' => 'config',
                    'description' => 'The config for metric component.',
                    'source' => __DIR__ . '/../publish/metric.php',
                    'destination' => BASE_PATH . '/config/autoload/metric.php',
                ],
            ],
            'listeners' => [
                DbQueryExecutedMetricListener::class,
                OnPipeMessage::class,
                OnMetricFactoryReady::class,
                OnBeforeHandle::class,
                OnWorkerStart::class,
                OnCoroutineServerStart::class,
                MetricBufferWatcher::class,
            ],
            'middlewares' => [
                MetricMiddleware::class,
            ],
            'processes' => [
                MetricProcess::class,
            ],
        ];
    }
}
