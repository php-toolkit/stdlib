<?php declare(strict_types=1);
/**
 * This file is part of toolkit/stdlib.
 *
 * @author   https://github.com/inhere
 * @link     https://github.com/php-toolkit/stdlib
 * @license  MIT
 */

namespace Toolkit\StdlibTest\Str;

use Toolkit\Stdlib\Str\UrlHelper;
use Toolkit\StdlibTest\BaseLibTestCase;

/**
 * class UrlHelperTest
 */
class UrlHelperTest extends BaseLibTestCase
{
    public function testJoinPath(): void
    {
        $this->assertEquals('a', UrlHelper::joinPath('a'));
        $this->assertEquals('a/b/cd', UrlHelper::joinPath('a', 'b', 'cd'));
    }
}
