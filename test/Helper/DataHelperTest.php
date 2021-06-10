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
}
