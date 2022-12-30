<?php declare(strict_types=1);
/**
 * This file is part of toolkit/stdlib.
 *
 * @author   https://github.com/inhere
 * @link     https://github.com/php-toolkit/stdlib
 * @license  MIT
 */

namespace Toolkit\StdlibTest\Arr;

use PHPUnit\Framework\TestCase;
use Toolkit\Stdlib\Arr;
use function array_keys;

/**
 * class ArrHelperTest
 */
class ArrHelperTest extends TestCase
{
    public function testIsList(): void
    {
        $this->assertTrue(Arr::isList(['a', 'b']));
        $this->assertFalse(Arr::isList(['a' => 'v0', 'b']));
        $this->assertTrue(Arr::isAssoc(['a' => 'v0', 'b']));
    }

    public function testToStringV2(): void
    {
        $this->assertEquals('[ab, 234]', Arr::toStringV2(['ab', 234]));
        $this->assertEquals('{k0: ab, k1: 234}', Arr::toStringV2(['k0' => 'ab', 'k1' => 234]));
    }

    public function testGetKeyMaxWidth(): void
    {
        $data = [
            'key1'      => 'value1',
            'key2-test' => 'value2',
        ];

        $this->assertSame(9, Arr::getKeyMaxWidth($data));
        $this->assertSame(9, Arr::getKeyMaxWidth($data, false));

        $data = [
            'key1'  => 'value1',
            '34430' => 'value2'
        ];

        $this->assertSame(4, Arr::getKeyMaxWidth($data));
        $this->assertSame(5, Arr::getKeyMaxWidth($data, false));
        $this->assertSame(5, Arr::getMaxWidth(array_keys($data), false));

        $data = [
            'key1' => 'value1',
            34430  => 'value2'
        ];

        $this->assertSame(4, Arr::getKeyMaxWidth($data));
        $this->assertSame(5, Arr::getKeyMaxWidth($data, false));
        $this->assertSame(5, Arr::getMaxWidth(array_keys($data), false));
    }
}
