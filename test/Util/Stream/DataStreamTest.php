<?php declare(strict_types=1);
/**
 * This file is part of toolkit/stdlib.
 *
 * @author   https://github.com/inhere
 * @link     https://github.com/php-toolkit/stdlib
 * @license  MIT
 */

namespace Toolkit\StdlibTest\Util\Stream;

use Toolkit\Stdlib\Util\Stream\DataStream;
use Toolkit\StdlibTest\BaseLibTestCase;
use function vdump;
use const SORT_NUMERIC;

/**
 * Class DataStreamTest
 *
 * @package Toolkit\StdlibTest\Util\Stream
 */
class DataStreamTest extends BaseLibTestCase
{
    public array $tests = [
        90, 19, 34, 8, 17
    ];

    public function testSorted(): void
    {
        $ds = DataStream::of([
            34, 90, 19, 8, 17
        ]);
        vdump($ds->toArray());

        $new = $ds->sorted();
        vdump($new->toArray());
        $this->assertNotEmpty($new->toArray());
        $this->assertEquals(8, $new->findFirst()->get());
        $this->assertEquals(90, $new->findLast()->get());
        $this->assertEquals(34, $ds->findFirst()->get());


        $new = $ds->sorted(DataStream::intComparer(true));

        $this->assertNotEmpty($new->toArray());
        vdump($new->toArray());

        $this->assertEquals(8, $new->findLast()->get());
        $this->assertEquals(90, $new->findFirst()->get());
    }

    public function testDistinct(): void
    {
        $ds = DataStream::of([
            34, 90, 19, 90, 34
        ]);

        $new = $ds->distinct(SORT_NUMERIC);
        // vdump($new->toArray());
        $this->assertCount(3, $new);
    }

    public function testMaxMin(): void
    {
        $ds = DataStream::of([
            34, 90, 19, 8, 17
        ]);

        $ret = $ds->max(DataStream::intComparer());
        $this->assertEquals(90, $ret->get());

        $ret = $ds->min(DataStream::intComparer());
        $this->assertEquals(8, $ret->get());
    }

    public function testMap(): void
    {
        $ds = DataStream::of([
            34, 90, 19, 8, 17
        ]);

        $new = $ds->map('strval');
        vdump($ds->toArray(), $new->toArray());
        $this->assertNotEmpty($ret = $new->toArray());
        $this->assertEquals(['34', '90', '19', '8', '17'], $ret);
    }

    public function testFlatMap(): void
    {
        $ds = DataStream::of([
            [34, 90],
            [19, 8, 17],
        ]);

        $new = $ds->flatMap(function (array $item) {
            return $item;
        });
        vdump($ds->toArray(), $new->toArray());
        $this->assertNotEmpty($ret = $new->toArray());
        $this->assertEquals([34, 90, 19, 8, 17], $ret);
    }
}
