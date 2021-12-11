<?php declare(strict_types=1);

namespace Toolkit\StdlibTest\Str;

use Toolkit\Stdlib\Helper\Assert;
use Toolkit\StdlibTest\BaseLibTestCase;

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
            $e = $this->tryCatchRun(fn() => Assert::notEmpty($val));
            $this->assertException($e, 'Expected a non-empty value');
        }

        $tests = [
            true,
        ];
        foreach ($tests as $val) {
            $e = $this->tryCatchRun(fn() => Assert::notEmpty($val));
            $this->assertException($e, 'NO ERROR', -1);
        }
    }

    public function testAssert_bool(): void
    {
        $e = $this->tryCatchRun(fn() => Assert::isFalse(false));
        $this->assertException($e, 'NO ERROR', -1);

        $e = $this->tryCatchRun(fn() => Assert::isFalse(true));
        $this->assertException($e, 'Expected a false value');
        $e = $this->tryCatchRun(fn() => Assert::isFalse(true, 'custom error'));
        $this->assertException($e, 'custom error');

        $e = $this->tryCatchRun(fn() => Assert::isTrue(true));
        $this->assertException($e, 'NO ERROR', -1);

        $e = $this->tryCatchRun(fn() => Assert::isTrue(false));
        $this->assertException($e, 'Expected a true value');
        $e = $this->tryCatchRun(fn() => Assert::isTrue(false, 'custom error'));
        $this->assertException($e, 'custom error');
    }
}
