<?php declare(strict_types=1);

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
