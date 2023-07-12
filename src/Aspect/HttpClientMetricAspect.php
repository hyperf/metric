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

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Promise\Create;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Psr7\Uri;
use Hyperf\Di\Aop\AroundInterface;
use Hyperf\Di\Aop\ProceedingJoinPoint;
use Hyperf\Metric\Support\Uri as SupportUri;
use Hyperf\Metric\Timer;
use Psr\Http\Message\ResponseInterface;

class HttpClientMetricAspect implements AroundInterface
{
    public array $classes = [Client::class . '::requestAsync'];

    public function process(ProceedingJoinPoint $proceedingJoinPoint)
    {
        $options = $proceedingJoinPoint->arguments['keys']['options'];

        if (isset($options['no_aspect']) && $options['no_aspect'] === true) {
            return $proceedingJoinPoint->process();
        }

        /** @var Client $instance */
        /** @var Uri $base_uri */
        $instance = $proceedingJoinPoint->getInstance();
        $base_uri = $instance->getConfig('base_uri');

        $arguments = $proceedingJoinPoint->arguments;
        $method = strtoupper($arguments['keys']['method'] ?? '');
        $uri = $arguments['keys']['uri'] ?? '';
        $host = $base_uri === null ? (parse_url($uri, PHP_URL_HOST) ?? '') : $base_uri->getHost();

        $labels = [
            'uri' => SupportUri::sanitize($uri),
            'host' => $host,
            'method' => $method,
            'http_status_code' => '200',
        ];

        $timer = new Timer('http_client_requests', $labels);

        /** @var PromiseInterface $result */
        $result = $proceedingJoinPoint->process();

        $result->then(
            $this->onFullFilled($timer, $labels),
            $this->onRejected($timer, $labels)
        );

        return $result;
    }

    private function onFullFilled(Timer $timer, array $labels): callable
    {
        return function (ResponseInterface $response) use ($timer, $labels) {
            $labels['http_status_code'] = (string) $response->getStatusCode();
            $timer->end($labels);
        };
    }

    private function onRejected(Timer $timer, array $labels): callable
    {
        return function (RequestException $exception) use ($timer, $labels) {
            $labels['http_status_code'] = (string) $exception->getResponse()->getStatusCode();
            $timer->end($labels);

            return Create::rejectionFor($exception);
        };
    }
}
