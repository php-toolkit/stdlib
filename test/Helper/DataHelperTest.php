<?php declare(strict_types=1);

namespace Toolkit\StdlibTest\Str;

use PHPUnit\Framework\TestCase;
use Toolkit\Stdlib\Helper\DataHelper;

/**
 * Class DataHelperTest
 *
 * @package Toolkit\StdlibTest\Str
 */
class DataHelperTest extends TestCase
{
    public function testToBool(): void
    {
        self::assertTrue(DataHelper::toBool(1));
        self::assertTrue(DataHelper::toBool('1'));
        self::assertTrue(DataHelper::toBool('on'));
        self::assertTrue(DataHelper::toBool('true'));
        self::assertFalse(DataHelper::toBool('false'));
        self::assertFalse(DataHelper::toBool('off'));
        self::assertFalse(DataHelper::toBool('0'));
    }

    public function testToString(): void
    {
        self::assertEquals('1.2', DataHelper::toString(1.2));
        self::assertEquals('12', DataHelper::toString(12));
        self::assertEquals('abc', DataHelper::toString('abc'));
        self::assertEquals('bool(TRUE)', DataHelper::toString(true));
        self::assertEquals('bool(FALSE)', DataHelper::toString(false));
        self::assertEquals('<NULL>', DataHelper::toString(null));
        self::assertEquals('["ab",23]', DataHelper::toString(['ab', 23]));

        $objStr = <<<OBJ
object(stdClass)#73 (2) {
  ["0"]=> string(2) "ab"
  ["1"]=> int(23)
}

OBJ;
        self::assertEquals($objStr, DataHelper::toString((object)['ab', 23]));
    }
}
