<?php declare(strict_types=1);
/**
 * This file is part of toolkit/stdlib.
 *
 * @author   https://github.com/inhere
 * @link     https://github.com/php-toolkit/stdlib
 * @license  MIT
 */

namespace Toolkit\Stdlib\Helper;

use PHPUnit\Framework\TestCase;
use ReflectionException;
use ReflectionMethod;
use RuntimeException;
use Throwable;

/**
 * class BasePhpTestCase
 *
 * @author inhere
 */
abstract class BasePhpTestCase extends TestCase
{
    /**
     * get method for test protected and private method
     *
     * usage:
     *
     * ```php
     * $rftMth = $this->method(SomeClass::class, $protectedOrPrivateMethod)
     *
     * $obj = new SomeClass();
     * $ret = $rftMth->invokeArgs($obj, $invokeArgs);
     * ```
     *
     * @param class-string|object $class
     * @param string $method
     *
     * @return ReflectionMethod
     * @throws ReflectionException
     */
    protected static function getMethod(string|object $class, string $method): ReflectionMethod
    {
        // $class  = new \ReflectionClass($class);
        // $method = $class->getMethod($method);

        $rftMth = new ReflectionMethod($class, $method);
        $rftMth->setAccessible(true);

        return $rftMth;
    }

    /**
     * @param callable $cb
     *
     * @return Throwable
     * @deprecated please use tryCatchRun()
     */
    protected function runAndGetException(callable $cb): Throwable
    {
        return $this->tryCatchRun($cb);
    }

    /**
     * @param callable $cb
     *
     * @return Throwable
     */
    protected function tryCatchRun(callable $cb): Throwable
    {
        try {
            $cb();
        } catch (Throwable $e) {
            return $e;
        }

        return new RuntimeException('NO ERROR', -1);
    }

    /**
     * @param Throwable $e
     * @param string $msg
     * @param int|null $code
     *
     * @return void
     */
    public function assertException(Throwable $e, string $msg, int $code = null): void
    {
        $this->assertEquals($msg, $e->getMessage());

        if ($code !== null) {
            $this->assertEquals($code, $e->getCode());
        }
    }
}
