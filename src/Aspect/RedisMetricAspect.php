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
namespace Hyperf\Metric\Aspect;

use Hyperf\Di\Aop\AroundInterface;
use Hyperf\Di\Aop\ProceedingJoinPoint;
use Hyperf\Metric\Timer;
use Hyperf\Redis\Redis;

class RedisMetricAspect implements AroundInterface
{
    public array $classes = [Redis::class . '::__call'];

    public function process(ProceedingJoinPoint $proceedingJoinPoint)
    {
        $arguments = $proceedingJoinPoint->arguments['keys'];

        $timer = new Timer('database_queries', [
            'system' => 'redis',
            'operation' => sprintf('Redis %s', $arguments['name']),
        ]);

        $result = $proceedingJoinPoint->process();
        $timer->end();

        return $result;
    }
}
