<?php declare(strict_types=1);

namespace Toolkit\StdlibTest\Util;

use Toolkit\Stdlib\Util\Optional;
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

        $val = $o->filter(static function ($val) {
            return $val > 25;
        })->orElse(25);

        $this->assertEquals(25, $val);
    }

    public function testOptional_or(): void
    {
        $o = Optional::ofNullable(null);

        $this->runAndGetException(function () use($o) {
            $o->get();
        });

        $val = $o->or(function () {
            return Optional::of(23);
        })->get();

        $this->assertEquals(23, $val);
    }
}
