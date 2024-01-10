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

use Hyperf\Database\Events\QueryExecuted;
use Hyperf\Event\Contract\ListenerInterface;
use Hyperf\Metric\Contract\MetricFactoryInterface;
use Hyperf\Metric\Contract\SqlSanitizerInterface;

use function Hyperf\Support\make;

class DbQueryExecutedMetricListener implements ListenerInterface
{
    public function __construct(private SqlSanitizerInterface $sqlSanitizer)
    {
    }

    public function listen(): array
    {
        return [QueryExecuted::class];
    }

    public function process(object $event): void
    {
        if (! $event instanceof QueryExecuted) {
            return;
        }

        $labels = [
            'system' => 'mysql',
            'operation' => $this->sqlSanitizer->sanitize($event->sql),
        ];

        $histogram = make(MetricFactoryInterface::class)
            ->makeHistogram('database_queries', array_keys($labels))
            ->with(...array_values($labels));

        $histogram->put($event->time);
    }
}
