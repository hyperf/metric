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
namespace HyperfTest\Metric\Cases;

use Hyperf\Metric\Support\SqlSanitizer;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
final class SqlSanitizerTest extends TestCase
{
    public function testSanitizeNumericIds(): void
    {
        $sanitizer = new SqlSanitizer();

        self::assertSame(
            'select * from `users` where `id` in (?)',
            $sanitizer->sanitize('select * from `users` where `id` in (200093484)')
        );

        self::assertSame(
            'select * from `bin_codes` where `bin_codes`.`bin` in (?)',
            $sanitizer->sanitize('select * from `bin_codes` where `bin_codes`.`bin` in (?)')
        );

        self::assertSame(
            'select * from `card_tokens` where (`card_id` = ?)',
            $sanitizer->sanitize('select * from `card_tokens` where (`card_id` = ?)')
        );

        self::assertSame(
            'select * from `cards` where `owner_id` not in (?, ?)',
            $sanitizer->sanitize('select * from `cards` where `owner_id` not in (1260361, 903023958)')
        );

        self::assertSame(
            'select * from `cards` where `owner_id` not in (?, ?, ?)',
            $sanitizer->sanitize('select * from `cards` where `owner_id` not in (1260361, 903023958, 880427302)')
        );
    }
}
