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

use Hyperf\Metric\Support\Uri;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
final class UriTest extends TestCase
{
    public function testSanitizeNumbers(): void
    {
        self::assertSame('/v1/test', Uri::sanitize('/v1/test'));
        self::assertSame('/v2/test/<NUMBER>', Uri::sanitize('/v2/test/123'));
        self::assertSame('/v3/test/<NUMBER>/bar', Uri::sanitize('/v3/test/123/bar'));
        self::assertSame('/v4/test/<NUMBER>/bar/<NUMBER>/', Uri::sanitize('/v4/test/123/bar/456/'));
        self::assertSame('/v5/test/<NUMBER>/<NUMBER>', Uri::sanitize('/v5/test/123/456'));
        self::assertSame('/v6/test/<NUMBER>/<NUMBER>/', Uri::sanitize('/v6/test/123/456/'));
        self::assertSame('/v7/test/<NUMBER>/<NUMBER>/<NUMBER>', Uri::sanitize('/v7/test/123/456/789'));
        self::assertSame('/v8/test/<NUMBER>/<NUMBER>/<NUMBER>/', Uri::sanitize('/v8/test/123/456/789/'));
    }

    public function testSanitizeLicensePlatesStrings(): void
    {
        self::assertSame('/v1/test', Uri::sanitize('/v1/test'));
        self::assertSame('/v2/test/<LICENSE-PLATE>', Uri::sanitize('/v2/test/PET9D49'));
        self::assertSame('/v2/test/<LICENSE-PLATE>', Uri::sanitize('/v2/test/PET9349'));
        self::assertSame('/v3/test/<LICENSE-PLATE>/bar', Uri::sanitize('/v3/test/PET9D49/bar'));
        self::assertSame('/v3/test/<LICENSE-PLATE>/bar', Uri::sanitize('/v3/test/PET9349/bar'));
        self::assertSame('/v4/test/<LICENSE-PLATE>/bar/<LICENSE-PLATE>/', Uri::sanitize('/v4/test/PET9D49/bar/PET9D49/'));
        self::assertSame('/v4/test/<LICENSE-PLATE>/bar/<LICENSE-PLATE>/', Uri::sanitize('/v4/test/PET9349/bar/PET9349/'));
        self::assertSame('/v5/test/<LICENSE-PLATE>/<LICENSE-PLATE>', Uri::sanitize('/v5/test/PET9D49/PET9D49'));
        self::assertSame('/v5/test/<LICENSE-PLATE>/<LICENSE-PLATE>', Uri::sanitize('/v5/test/PET9349/PET9349'));
        self::assertSame('/v6/test/<LICENSE-PLATE>/<LICENSE-PLATE>/', Uri::sanitize('/v6/test/PET9D49/PET9D49/'));
        self::assertSame('/v6/test/<LICENSE-PLATE>/<LICENSE-PLATE>/', Uri::sanitize('/v6/test/PET9349/PET9349/'));
        self::assertSame('/v7/test/<LICENSE-PLATE>/<LICENSE-PLATE>/<LICENSE-PLATE>', Uri::sanitize('/v7/test/PET9D49/PET9D49/PET9D49'));
        self::assertSame('/v7/test/<LICENSE-PLATE>/<LICENSE-PLATE>/<LICENSE-PLATE>', Uri::sanitize('/v7/test/PET9349/PET9349/PET9349'));
        self::assertSame('/v8/test/<LICENSE-PLATE>/<LICENSE-PLATE>/<LICENSE-PLATE>/', Uri::sanitize('/v8/test/PET9D49/PET9D49/PET9D49/'));
        self::assertSame('/v8/test/<LICENSE-PLATE>/<LICENSE-PLATE>/<LICENSE-PLATE>/', Uri::sanitize('/v8/test/PET9349/PET9349/PET9349/'));
    }

    public function testClearUriUuids(): void
    {
        $uuid = '123e4567-e89b-12d3-a456-426614174000';

        self::assertSame('/v1/test', Uri::sanitize('/v1/test'));
        self::assertSame('/v2/test/<UUID>', Uri::sanitize("/v2/test/{$uuid}"));
        self::assertSame('/v3/test/<UUID>/bar', Uri::sanitize("/v3/test/{$uuid}/bar"));
        self::assertSame('/v4/test/<UUID>/bar/<UUID>/', Uri::sanitize("/v4/test/{$uuid}/bar/{$uuid}/"));
        self::assertSame('/v5/test/<UUID>/<UUID>', Uri::sanitize("/v5/test/{$uuid}/{$uuid}"));
        self::assertSame('/v6/test/<UUID>/<UUID>/', Uri::sanitize("/v6/test/{$uuid}/{$uuid}/"));
        self::assertSame('/v7/test/<UUID>/<UUID>/<UUID>', Uri::sanitize("/v7/test/{$uuid}/{$uuid}/{$uuid}"));
        self::assertSame('/v8/test/<UUID>/<UUID>/<UUID>/', Uri::sanitize("/v8/test/{$uuid}/{$uuid}/{$uuid}/"));
    }

    public function testClearUriOids(): void
    {
        $oid = '650229807612bba4984d1fc7';
        $oidShort = '65022612bba84d1f';

        self::assertSame('/v1/test', Uri::sanitize('/v1/test'));
        self::assertSame('/v2/test/<OID>', Uri::sanitize("/v2/test/{$oid}"));
        self::assertSame('/v3/test/<OID>/bar', Uri::sanitize("/v3/test/{$oid}/bar"));
        self::assertSame('/v4/test/<OID>/bar/<OID>/', Uri::sanitize("/v4/test/{$oid}/bar/{$oid}/"));
        self::assertSame('/v5/test/<OID>/<OID>', Uri::sanitize("/v5/test/{$oid}/{$oid}"));
        self::assertSame('/v6/test/<OID>/<OID>/', Uri::sanitize("/v6/test/{$oid}/{$oid}/"));
        self::assertSame('/v7/test/<OID>/<OID>/<OID>', Uri::sanitize("/v7/test/{$oid}/{$oid}/{$oid}"));
        self::assertSame('/v8/test/<OID>/<OID>/<OID>/', Uri::sanitize("/v8/test/{$oid}/{$oid}/{$oid}/"));
        self::assertSame('/v9/test/<OID>/bar/<NUMBER>', Uri::sanitize("/v9/test/{$oidShort}/bar/12345"));
    }

    public function testAddsInitialSlash(): void
    {
        self::assertSame('/v1/', Uri::sanitize('/v1/'));
        self::assertSame('/v1', Uri::sanitize('v1'));
        self::assertSame('/v1/', Uri::sanitize('v1/'));
        self::assertSame('/v1/test/', Uri::sanitize('/v1/test/'));
        self::assertSame('/v1/test', Uri::sanitize('v1/test'));
        self::assertSame('/v1/test/', Uri::sanitize('v1/test/'));
    }

    public function testAndroidId(): void
    {
        self::assertSame('/device/<ANDROID-ID>/user/<NUMBER>', Uri::sanitize('/devices/a436d9ffefef80e8/user/999'));
        self::assertSame('/device/<ANDROID-ID>/user/<NUMBER>', Uri::sanitize('/devices/7b5d68f217d90ff5/user/999'));
        self::assertSame('/device/<ANDROID-ID>/user/<NUMBER>', Uri::sanitize('/devices/dc900fb903cc308c/user/999'));
        self::assertSame('/device/<ANDROID-ID>/user/<NUMBER>', Uri::sanitize('/devices/86d144c9078c8176/user/999'));
        self::assertSame('/device/<ANDROID-ID>/user/<NUMBER>', Uri::sanitize('/devices/86d144c9078c8176/user/8045169'));
    }

    public function testSanitizeHashsStrings(): void
    {
        self::assertSame('/v1/test', Uri::sanitize('/v1/test'));
        self::assertSame('/v2/test/<SHA1>', Uri::sanitize('/v2/test/141da78905dcaa7ed8d4da7c3f49a2415ebdc110'));
        self::assertSame('/v2/test/<SHA1>', Uri::sanitize('/v2/test/7110EDA4D09E062AA5E4A390B0A572AC0D2C0220'));
        self::assertSame('/v3/test/<SHA1>/bar', Uri::sanitize('/v3/test/81FE8BFE87576C3ECB22426F8E57847382917ACF/bar'));
        self::assertSame('/v3/test/<SHA1>/bar', Uri::sanitize('/v3/test/7110EDA4D09E062AA5E4A390B0A572AC0D2C0220/bar'));
        self::assertSame('/v4/test/<SHA1>/bar/<SHA1>/', Uri::sanitize('/v4/test/141da78905dcaa7ed8d4da7c3f49a2415ebdc110/bar/141da78905dcaa7ed8d4da7c3f49a2415ebdc110/'));
        self::assertSame('/v4/test/<SHA1>/bar/<SHA1>/', Uri::sanitize('/v4/test/7110EDA4D09E062AA5E4A390B0A572AC0D2C0220/bar/7110EDA4D09E062AA5E4A390B0A572AC0D2C0220/'));
        self::assertSame('/v5/test/<SHA1>/<SHA1>', Uri::sanitize('/v5/test/141da78905dcaa7ed8d4da7c3f49a2415ebdc110/141da78905dcaa7ed8d4da7c3f49a2415ebdc110'));
        self::assertSame('/v5/test/<SHA1>/<SHA1>', Uri::sanitize('/v5/test/7110EDA4D09E062AA5E4A390B0A572AC0D2C0220/7110EDA4D09E062AA5E4A390B0A572AC0D2C0220'));
        self::assertSame('/v6/test/<SHA1>/<SHA1>/', Uri::sanitize('/v6/test/141da78905dcaa7ed8d4da7c3f49a2415ebdc110/141da78905dcaa7ed8d4da7c3f49a2415ebdc110/'));
        self::assertSame('/v6/test/<SHA1>/<SHA1>/', Uri::sanitize('/v6/test/7110EDA4D09E062AA5E4A390B0A572AC0D2C0220/7110EDA4D09E062AA5E4A390B0A572AC0D2C0220/'));
        self::assertSame('/v7/test/<SHA1>/<SHA1>/<SHA1>', Uri::sanitize('/v7/test/141da78905dcaa7ed8d4da7c3f49a2415ebdc110/141da78905dcaa7ed8d4da7c3f49a2415ebdc110/141da78905dcaa7ed8d4da7c3f49a2415ebdc110'));
        self::assertSame('/v7/test/<SHA1>/<SHA1>/<SHA1>', Uri::sanitize('/v7/test/7110EDA4D09E062AA5E4A390B0A572AC0D2C0220/7110EDA4D09E062AA5E4A390B0A572AC0D2C0220/7110EDA4D09E062AA5E4A390B0A572AC0D2C0220'));
        self::assertSame('/v8/test/<SHA1>/<SHA1>/<SHA1>/', Uri::sanitize('/v8/test/141da78905dcaa7ed8d4da7c3f49a2415ebdc110/141da78905dcaa7ed8d4da7c3f49a2415ebdc110/141da78905dcaa7ed8d4da7c3f49a2415ebdc110/'));
        self::assertSame('/v8/test/<SHA1>/<SHA1>/<SHA1>/', Uri::sanitize('/v8/test/7110EDA4D09E062AA5E4A390B0A572AC0D2C0220/7110EDA4D09E062AA5E4A390B0A572AC0D2C0220/7110EDA4D09E062AA5E4A390B0A572AC0D2C0220/'));
    }
}
