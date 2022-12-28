<?php declare(strict_types=1);
/**
 * This file is part of toolkit/stdlib.
 *
 * @author   https://github.com/inhere
 * @link     https://github.com/php-toolkit/stdlib
 * @license  MIT
 */

namespace Toolkit\StdlibTest\Str;

use PHPUnit\Framework\TestCase;
use Toolkit\Stdlib\Str\StrValue;

/**
 * class StrValueTest
 *
 * @author inhere
 * @date 2022/12/27
 */
class StrValueTest extends TestCase
{
    public function testStrObjectBasic(): void
    {
        $s = StrValue::new('abc ');

        self::assertFalse($s->hasSuffix('c'));
        self::assertEquals(4, $s->length());
        self::assertEquals('abc', $s->trimmed()->value());
        self::assertEquals('abc', $s->trim()->toString());

        $s->trim();

        self::assertEquals(3, $s->length());
        self::assertTrue($s->contains('b'));
        self::assertTrue($s->hasPrefix('a'));
        self::assertTrue($s->hasSuffix('c'));
    }
}
