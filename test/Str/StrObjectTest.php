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
use Toolkit\Stdlib\Str\StrObject;

class StrObjectTest extends TestCase
{
    public function testStrObjectBasic(): void
    {
        $s = StrObject::new('abc ');

        self::assertEquals(4, $s->length());
        self::assertEquals('abc', $s->trimmed());
        self::assertFalse($s->hasSuffix('c'));

        $s->trim();

        self::assertEquals(3, $s->length());
        self::assertTrue($s->contains('b'));
        self::assertTrue($s->hasPrefix('a'));
        self::assertTrue($s->hasSuffix('c'));
    }
}
