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
namespace Hyperf\Metric\Adapter\RemoteProxy;

use Hyperf\Context\ApplicationContext;
use Hyperf\Metric\Contract\GaugeInterface;
use Hyperf\Metric\Contract\MetricCollectorInterface;

class Gauge implements GaugeInterface
{
    /**
     * @var string[]
     */
    public array $labelValues = [];

    public ?float $delta;

    public ?float $value;

    public function __construct(public string $name, public array $labelNames)
    {
    }

    public function with(string ...$labelValues): static
    {
        $this->labelValues = $labelValues;
        return $this;
    }

    public function set(float $value): void
    {
        $this->value = $value;
        $this->delta = null;

        ApplicationContext::getContainer()
            ->get(MetricCollectorInterface::class)
            ->add($this);
    }

    public function add(float $delta): void
    {
        $this->delta = $delta;
        $this->value = null;

        ApplicationContext::getContainer()
            ->get(MetricCollectorInterface::class)
            ->add($this);
    }
}
