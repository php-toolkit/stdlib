<?php declare(strict_types=1);

namespace Toolkit\StdlibTest\Str;

use PHPUnit\Framework\TestCase;
use Toolkit\Stdlib\Str\StrObject;

class StrObjectTest extends TestCase
{
    public function testBasic(): void
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
