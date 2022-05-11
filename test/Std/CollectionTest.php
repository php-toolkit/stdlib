<?php declare(strict_types=1);
/**
 * This file is part of toolkit/stdlib.
 *
 * @author   https://github.com/inhere
 * @link     https://github.com/php-toolkit/stdlib
 * @license  MIT
 */

namespace Toolkit\StdlibTest\Std;

use Toolkit\Stdlib\Std\Collection;
use Toolkit\StdlibTest\BaseLibTestCase;

/**
 * class CollectionTest
 *
 * @author inhere
 */
class CollectionTest extends BaseLibTestCase
{
    public function testCollection_basic(): void
    {
        $c = Collection::new(['age' => 23, 'name' => 'inhere']);

        $this->assertTrue($c->has('age'));
        $this->assertEquals(23, $c->get('age'));
        $this->assertEquals(23, $c->getInt('age'));
        $this->assertEquals('23', $c->getString('age'));
    }
}
