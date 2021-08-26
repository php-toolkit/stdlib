<?php declare(strict_types=1);

namespace Toolkit\StdlibTest\Arr;

use PHPUnit\Framework\TestCase;
use Toolkit\Stdlib\Arr;
use function array_keys;

/**
 * class ArrHelperTest
 */
class ArrHelperTest extends TestCase
{
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
