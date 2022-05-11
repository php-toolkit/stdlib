<?php declare(strict_types=1);
/**
 * This file is part of toolkit/stdlib.
 *
 * @author   https://github.com/inhere
 * @link     https://github.com/php-toolkit/stdlib
 * @license  MIT
 */

namespace Toolkit\StdlibTest\Str;

use Toolkit\Stdlib\Helper\Assert;
use Toolkit\StdlibTest\BaseLibTestCase;
use const STDOUT;

/**
 * class AssertTest
 *
 * @author inhere
 */
class AssertTest extends BaseLibTestCase
{
    public function testAssert_notEmpty(): void
    {
        $tests = [
            false,
            null,
            0,
            0.0,
            '',
            '0',
            [],
        ];
        foreach ($tests as $val) {
            $e = $this->tryCatchRun(fn () => Assert::notEmpty($val));
            $this->assertException($e, 'Expected a non-empty value');
        }

        $tests = [
            true,
        ];
        foreach ($tests as $val) {
            $e = $this->tryCatchRun(fn () => Assert::notEmpty($val));
            $this->assertException($e, 'NO ERROR', -1);
        }
    }

    public function testAssert_bool(): void
    {
        $e = $this->tryCatchRun(fn () => Assert::isFalse(false));
        $this->assertException($e, 'NO ERROR', -1);

        $e = $this->tryCatchRun(fn () => Assert::isFalse(true));
        $this->assertException($e, 'Expected a false value');
        $e = $this->tryCatchRun(fn () => Assert::isFalse(true, 'custom error'));
        $this->assertException($e, 'custom error');

        $e = $this->tryCatchRun(fn () => Assert::isTrue(true));
        $this->assertException($e, 'NO ERROR', -1);

        $e = $this->tryCatchRun(fn () => Assert::isTrue(false));
        $this->assertException($e, 'Expected a true value');
        $e = $this->tryCatchRun(fn () => Assert::isTrue(false, 'custom error'));
        $this->assertException($e, 'custom error');
    }

    public function testAssert_FS(): void
    {
        $e = $this->tryCatchRun(fn () => Assert::isFile(__DIR__ . '/AssertTest.php'));
        $this->assertException($e, 'NO ERROR', -1);

        $e = $this->tryCatchRun(fn () => Assert::isFile('./not-exists.file'));
        $this->assertException($e, 'No such file: ./not-exists.file');

        $e = $this->tryCatchRun(fn () => Assert::isDir(__DIR__));
        $this->assertException($e, 'NO ERROR', -1);

        $e = $this->tryCatchRun(fn () => Assert::isDir('./not-exists'));
        $this->assertException($e, 'No such dir: ./not-exists');

        $e = $this->tryCatchRun(fn () => Assert::isResource('invalid'));
        $this->assertException($e, 'Excepted an resource');

        $e = $this->tryCatchRun(fn () => Assert::isResource(STDOUT));
        $this->assertException($e, 'NO ERROR', -1);
    }
}
