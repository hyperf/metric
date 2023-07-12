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
 * Histogram describes a metric that takes repeated observations of the same
 * kind of thing, and produces a statistical summary of those observations,
 * typically expressed as quantiles or buckets. An example of a histogram is
 * HTTP request latencies.
 */
interface HistogramInterface
{
    public function with(string ...$labelValues): static;

    public function put(float $sample): void;
}
