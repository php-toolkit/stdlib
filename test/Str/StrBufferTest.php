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
use Toolkit\Stdlib\Str\StrBuffer;

/**
 * Class StrBufferTest
 *
 * @package Toolkit\StdlibTest\Str
 */
class StrBufferTest extends TestCase
{
    public function testBasic(): void
    {
        $buf = StrBuffer::new();

        $buf->write('a');
        $buf->writef(' %s an ', 'is');
        $buf->writeln('alpha');

        self::assertEquals("a is an alpha\n", $buf->toString());

        $buf->reset();
        self::assertEmpty($buf->toString());
    }
}
