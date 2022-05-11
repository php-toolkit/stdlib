<?php declare(strict_types=1);
/**
 * This file is part of toolkit/stdlib.
 *
 * @author   https://github.com/inhere
 * @link     https://github.com/php-toolkit/stdlib
 * @license  MIT
 */

namespace Toolkit\StdlibTest\Obj\Traits;

use Toolkit\StdlibTest\BaseLibTestCase;
use Toolkit\StdlibTest\Cases\AutoConfigObj;

/**
 * class AutoConfigTraitTest
 *
 * @author inhere
 */
class AutoConfigTraitTest extends BaseLibTestCase
{
    public function testAutoConfig(): void
    {
        $obj = AutoConfigObj::new();

        $this->assertEquals(0, $obj->age);
        $this->assertEquals('', $obj->getName());

        $obj = AutoConfigObj::new(['age' => 23, 'name' => 'inhere']);
        $this->assertEquals(23, $obj->age);
        $this->assertEquals('inhere', $obj->getName());
    }
}
