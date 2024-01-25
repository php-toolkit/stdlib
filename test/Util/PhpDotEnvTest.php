<?php declare(strict_types=1);
/**
 * This file is part of toolkit/stdlib.
 *
 * @author   https://github.com/inhere
 * @link     https://github.com/php-toolkit/stdlib
 * @license  MIT
 */

namespace Toolkit\StdlibTest\Util;

use PHPUnit\Framework\TestCase;
use Toolkit\Stdlib\Util\PhpDotEnv;

/**
 * class PhpDotEnv
 */
class PhpDotEnvTest extends TestCase
{

    public function testDotEnv_basic(): void
    {
        $fileDir = __DIR__ . '/../' . 'test.env';

        $this->assertFalse(getenv('PHPDOTENV_TEST'));

        PhpDotEnv::load($fileDir);
        $this->assertEquals('test_val', getenv('PHPDOTENV_TEST'));
    }
}
