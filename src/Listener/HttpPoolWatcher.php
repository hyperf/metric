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

namespace Hyperf\Metric\Listener;

use Hyperf\Contract\ConfigInterface;
use Hyperf\Contract\ContainerInterface;
use Hyperf\Coordinator\Timer;
use Hyperf\Event\Contract\ListenerInterface;
use Hyperf\Framework\Event\BeforeWorkerStart;
use Hyperf\Metric\Metric;
use Hyperf\Pool\SimplePool\PoolFactory;
use Hyperf\Server\Event\MainCoroutineServerStart;

class HttpPoolWatcher implements ListenerInterface
{
    private const GUZZLE_PREFIX = 'guzzle.handler';

    protected Timer $timer;

    public function __construct(protected ContainerInterface $container)
    {
        $this->timer = new Timer();
    }

    public function listen(): array
    {
        return [
            BeforeWorkerStart::class,
            MainCoroutineServerStart::class,
        ];
    }

    public function process(object $event): void
    {
        /** @var PoolFactory $factory */
        $factory = $this->container->get(PoolFactory::class);
        $worker = (string) ($event->workerId ?? 0);

        $config = $this->container->get(ConfigInterface::class);

        $timer_interval = $config->get('metric.default_metric_interval', 5);

        $this->timer->tick($timer_interval, function () use ($factory, $worker) {
            foreach ($factory->getPoolNames() as $name) {
                if (strpos($name, self::GUZZLE_PREFIX) !== 0) {
                    continue;
                }

                $pool = $factory->get($name, function () {});

                $labels = [
                    'worker' => $worker,
                    'pool' => implode('.', array_slice(explode('.', (string) $name), 2, -2)),
                ];

                Metric::gauge('http_connections_in_use', (float) $pool->getCurrentConnections(), $labels);
                Metric::gauge('http_connections_in_waiting', (float) $pool->getConnectionsInChannel(), $labels);
                Metric::gauge('http_max_connections', (float) $pool->getOption()->getMaxConnections(), $labels);
            }
        });
    }
}
