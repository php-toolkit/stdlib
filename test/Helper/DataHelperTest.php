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
use Toolkit\Stdlib\Helper\DataHelper;
use Toolkit\Stdlib\Str;

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
        self::assertEquals('NULL', DataHelper::toString(null));
        self::assertEquals('["ab",23]', DataHelper::toString(['ab', 23]));

        $str = DataHelper::toString((object)['ab', 23]);
        self::assertTrue(Str::contains($str, 'object(stdClass)'));
    }
}
