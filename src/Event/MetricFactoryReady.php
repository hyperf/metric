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
namespace Hyperf\Metric\Event;

use Hyperf\Metric\Contract\MetricFactoryInterface;

class MetricFactoryReady
{
    /**
     * @param MetricFactoryInterface $factory a ready to use factory
     */
    public function __construct(public MetricFactoryInterface $factory)
    {
    }
}
