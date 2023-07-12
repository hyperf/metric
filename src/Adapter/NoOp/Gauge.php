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
namespace Hyperf\Metric\Adapter\NoOp;

use Hyperf\Metric\Contract\GaugeInterface;

class Gauge implements GaugeInterface
{
    public function with(string ...$labelValues): static
    {
        return $this;
    }

    public function set(float $value): void
    {
    }

    public function add(float $delta): void
    {
    }
}
