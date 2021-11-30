<?php declare(strict_types=1);

namespace Toolkit\StdlibTest\Obj;

use Toolkit\Stdlib\Obj\ObjectBox;
use Toolkit\StdlibTest\BaseLibTestCase;

/**
 * class ObjectBoxTest
 *
 * @author inhere
 */
class ObjectBoxTest extends BaseLibTestCase
{
    public function testObjectBox_basic(): void
    {
        $box = ObjectBox::new();
        $box->set('name1', 'inhere');

        $this->assertTrue($box->has('name1'));
        $this->assertEquals('inhere', $box->get('name1'));
    }
}
