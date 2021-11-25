<?php declare(strict_types=1);

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

        $this->assertEquals(['23', '34'], $int->mapToString()->toArray());
    }
}
