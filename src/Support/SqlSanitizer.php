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
namespace Hyperf\Metric\Support;

use Hyperf\Metric\Contract\SqlSanitizerInterface;

class SqlSanitizer implements SqlSanitizerInterface
{
    public function sanitize(string $sql): string
    {
        $patterns = [
            '/(?<=\()\d+(?=\))/',
            '/(?<=\()\d+(?=,\s)/',
            '/(?<=,\s)\d+(?=,\s)/',
            '/(?<=,\s)\d+(?=\))/',
        ];

        return preg_replace($patterns, array_fill(0, count($patterns), '?'), $sql);
    }
}
