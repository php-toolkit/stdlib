<?php declare(strict_types=1);

namespace Toolkit\StdlibTest\Str;

use PHPUnit\Framework\TestCase;
use Toolkit\Stdlib\Str;

/**
 * Class StringHelperTest
 *
 * @package Toolkit\StdlibTest\Str
 */
class StringHelperTest extends TestCase
{
    public function testShellQuote(): void
    {
        $tests = [
            ['', ''],
            ['abc', 'abc'],
            ['ab"c', 'ab"c'],
        ];
        foreach ($tests as [$given, $want]) {
            self::assertSame($want, Str::shellQuote($given));
        }
    }

    public function testStrLen(): void
    {
        $tests = [
            ['abc', 3],
            [123, 3],
            [123.4, 5],
            ['23ab', 4],
        ];

        foreach ($tests as [$case, $want]) {
            $this->assertSame($want, Str::strlen($case));
        }
    }
}
