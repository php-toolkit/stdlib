<?php declare(strict_types=1);
/**
 * This file is part of toolkit/stdlib.
 *
 * @author   https://github.com/inhere
 * @link     https://github.com/php-toolkit/stdlib
 * @license  MIT
 */

namespace Toolkit\StdlibTest\Util\Stream;

use Toolkit\Stdlib\Util\Stream\IntStream;
use Toolkit\StdlibTest\BaseLibTestCase;

/**
 * class IntStreamTest
 *
 * @author inhere
 */
class IntStreamTest extends BaseLibTestCase
{
    public function testIntStream_mapToString(): void
    {
        $int = IntStream::of([23, 34]);

        $this->assertEquals(['23', '34'], $int->mapToString('strval')->toArray());
    }
}
