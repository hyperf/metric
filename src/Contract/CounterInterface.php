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
 * Counter describes a metric that accumulates values monotonically.
 * An example of a counter is the number of received HTTP requests.
 */
interface CounterInterface
{
    public function with(string ...$labelValues): static;

    public function add(int $delta): void;
}
