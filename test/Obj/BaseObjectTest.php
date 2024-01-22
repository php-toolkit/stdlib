<?php declare(strict_types=1);

namespace Toolkit\StdlibTest\Obj;

use Toolkit\StdlibTest\BaseLibTestCase;

/**
 * @author inhere
 */
class BaseObjectTest extends BaseLibTestCase
{

    public function testUseSnake(): void
    {
        // use snake
        $po = new ADemoPo2(['one_two' => 'abc']);
        $this->assertEquals('abc', $po->oneTwo);

        // load real name
        $po->load(['oneTwo' => 'b235', 'notExists' => '234', 'str' => '']);
        $this->assertEquals('b235', $po->oneTwo);
    }

    public function testArray(): void
    {
        // array map
        $po = new ADemoPo2([
            'arr' => [
                'int'  => 1,
                'int2' => 0,
                'str'  => 'abc',
                'str1' => '',
                'arr'  => [],
                'bol'  => false,
            ],
        ]);

        $data = $po->toCleaned();
        vdump($data);
        $this->assertArrayHasKey('arr', $data);
        $arr = $data['arr'];
        $this->assertArrayNotHasKey('int2', $arr);
        $this->assertArrayNotHasKey('str1', $arr);

        $po->arr = ['a', '', 'c'];
        $data = $po->toCleaned();
        vdump($data);
        $this->assertArrayHasKey('arr', $data);
        $arr = $data['arr'];
        $this->assertCount(3, $arr);
    }

    public function testSubArray(): void
    {
        // use snake
        $po = new ADemoPo2([
            'str' => 'abc',
            'obj'     => new ADemoPo2([
                'int'  => 1,
                'int2' => 0,
                'str'  => 'abc',
                'str1' => '',
                'arr'  => [],
            ]),
            'arrList' => [
                [
                    'sid'          => 2070,
                    'points'       => 0,
                    'give'         => 0,
                    'comment'      => 0,
                    'recharge'     => 12299,
                    'invitation'   => 0,
                    'rechargeGive' => 1,
                ],
            ],
        ]);

        $this->assertEquals('abc', $po->str);

        vdump($data = $po->toCleaned());
        $this->assertArrayHasKey('str', $data);

        $this->assertArrayHasKey('obj', $data);
        $this->assertArrayNotHasKey('int2', $data['obj']);
        $this->assertArrayNotHasKey('str1', $data['obj']);

        // sub array
        $this->assertArrayHasKey('arrList', $data);
        $this->assertNotEmpty($subItem = $data['arrList'][0]);
        $this->assertArrayHasKey('sid', $subItem);
        $this->assertArrayNotHasKey('give', $subItem);
        $this->assertArrayNotHasKey('comment', $subItem);

        $po->load([
            'objList' => [
                new ADemoPo2([
                    'int'  => 1,
                    'int2' => 0,
                    'str'  => 'def',
                    'str1' => '',
                    'arr'  => [],
                ]),
            ],
        ]);
        vdump($data = $po->toCleaned());

        // sub object
        $this->assertArrayHasKey('objList', $data);
        $this->assertNotEmpty($subItem = $data['objList'][0]);
        $this->assertArrayHasKey('str', $subItem);
        $this->assertArrayNotHasKey('str1', $subItem);
    }

    public function testUseJsonMap(): void
    {
        // use snake
        $po = new ADemoPo2(['one_two' => 'abc']);
        $this->assertEquals('abc', $po->oneTwo);

        // use alias name
        $po->load(['ot' => 'd34'], ['ot' => 'oneTwo']);
        $this->assertEquals('d34', $po->oneTwo);

        // use alias name in class _aliasMap
        $po->load(['ot2' => 'a23']);
        $this->assertEquals('a23', $po->oneTwo2);

        // to array map
        $arr = $po->setValue('str', '')->toMap();
        vdump(get_object_vars($po), $arr);
        $this->assertArrayHasKey('str', $arr);

        $arr = $po->toMap(true);
        vdump($arr);
        $this->assertArrayHasKey('oneTwo', $arr);
        $this->assertArrayHasKey('oneTwo2', $arr);
        $this->assertArrayNotHasKey('str', $arr);

        // to aliased
        $arr = $po->toAliased(true);
        vdump($arr);
        $this->assertArrayHasKey('oneTwo', $arr);
        $this->assertArrayHasKey('ot2', $arr);
        $this->assertArrayNotHasKey('oneTwo2', $arr);
        $this->assertArrayNotHasKey('str', $arr);
    }
}
