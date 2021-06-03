<?php declare(strict_types=1);

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
        self::assertNotEmpty(OS::useHomeDir());
    }
}
