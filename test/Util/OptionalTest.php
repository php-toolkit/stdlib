<?php declare(strict_types=1);
/**
 * This file is part of toolkit/stdlib.
 *
 * @author   https://github.com/inhere
 * @link     https://github.com/php-toolkit/stdlib
 * @license  MIT
 */

namespace Toolkit\StdlibTest\Util;

use Toolkit\Stdlib\Util\Optional;
use Toolkit\Stdlib\Util\Stream\IntStream;
use Toolkit\StdlibTest\BaseLibTestCase;

/**
 * class OptionalTest
 *
 * @author inhere
 */
class OptionalTest extends BaseLibTestCase
{
    public function testOptionalBasic(): void
    {
        $o = Optional::of(23);
        $this->assertEquals(23, $o->get());
        $this->assertEquals('23', $o->map('strval')->get());

        // use arrow syntax:
        $val = $o->filter(fn ($val) => $val > 25)->orElse(25);
        $this->assertEquals(25, $val);

        $o = Optional::nullable(null);
        $this->assertEquals(23, $o->orElse(23));
        $this->assertEquals(25, $o->orElseGet(fn () => 25));
    }

    public function testOptional_stream(): void
    {
        $o = Optional::nullable([23, '56', '78']);

        $this->assertEquals([23, '56', '78'], $o->get());
        $list = $o->stream()->map(fn ($val) => (int)$val)->toArray();
        $this->assertEquals([23, 56, 78], $list);

        $this->assertEquals([23, '56', '78'], $o->get());
        $list = $o->stream(IntStream::class)->toArray();
        $this->assertEquals([23, 56, 78], $list);
    }

    public function testOptional_or(): void
    {
        $o = Optional::ofNullable(null);
        $e = $this->tryCatchRun(fn () => $o->get());
        $this->assertException($e, 'No value present');

        $val = $o->or(fn () => Optional::of(23))->get();
        $this->assertEquals(23, $val);

        $val = $o->or(fn () => Optional::of('abc'))->get();
        $this->assertEquals('abc', $val);
    }
}
