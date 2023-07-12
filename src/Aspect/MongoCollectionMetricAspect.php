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
use ReflectionProperty;

class MongoCollectionMetricAspect implements AroundInterface
{
    public array $classes = [
        'Hyperf\GoTask\MongoClient\Collection',
    ];

    public array $annotations = [];

    protected array $ignoredMethods = [
        'makePayload',
    ];

    public function process(ProceedingJoinPoint $proceedingJoinPoint)
    {
        if (in_array($proceedingJoinPoint->methodName, $this->ignoredMethods)) {
            return $proceedingJoinPoint->process();
        }

        $collectionName = $this->getCollectionName($proceedingJoinPoint);
        $method = $proceedingJoinPoint->methodName;

        $timer = new Timer('database_queries', [
            'system' => 'mongodb',
            'operation' => sprintf('MongoDB %s %s', $collectionName, $method),
        ]);

        $result = $proceedingJoinPoint->process();
        $timer->end();

        return $result;
    }

    private function getCollectionName(ProceedingJoinPoint $proceedingJoinPoint): string
    {
        $collection = $proceedingJoinPoint->getInstance();

        $property = new ReflectionProperty('Hyperf\GoTask\MongoClient\Collection', 'collection');
        $property->setAccessible(true);

        return $property->getValue($collection);
    }
}
