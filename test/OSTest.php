<?php declare(strict_types=1);
/**
 * This file is part of toolkit/stdlib.
 *
 * @author   https://github.com/inhere
 * @link     https://github.com/php-toolkit/stdlib
 * @license  MIT
 */

namespace Toolkit\StdlibTest;

use PHPUnit\Framework\TestCase;
use Toolkit\Stdlib\OS;

/**
 * Class OSTest
 *
 * @package Toolkit\StdlibTest
 */
class OSTest extends TestCase
{
    public function testGetUserHomeDir(): void
    {
        self::assertNotEmpty(OS::userHomeDir());
    }
}
