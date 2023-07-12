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
namespace Hyperf\Metric\Contract;

/**
 * Gauge describes a metric that takes specific values over time.
 * An example of a gauge is the current depth of a job queue.
 */
interface GaugeInterface
{
    public function with(string ...$labelValues): static;

    public function set(float $value): void;

    public function add(float $delta): void;
}
