<?php declare(strict_types=1);

namespace Toolkit\StdlibTest\Obj;

use Toolkit\Stdlib\Obj\AbstractObj;

/**
 * @author inhere
 */
class ADemoPo2 extends AbstractObj
{
    protected array $_jsonMap = [
        'ot2' => 'oneTwo2',
    ];

    /**
     * @var bool
     */
    public bool $bol;

    /**
     * @var bool
     */
    public bool $bol1;

    /**
     * int
     *
     * @var integer
     */
    public int $int;

    /**
     * int2
     *
     * @var integer
     */
    public int $int2;

    /**
     * str
     *
     * @var string
     */
    public string $str;

    /**
     * str1
     *
     * @var string
     */
    public string $str1;

    /**
     * @var string
     */
    public string $oneTwo;

    /**
     * @var string
     */
    public string $oneTwo2;

    /**
     * arr
     *
     * @var array
     */
    public array $arr;

    /**
     * arr list
     *
     * @var array[]
     */
    public array $arrList;

    /**
     * obj
     *
     * @var self
     */
    public ADemoPo2 $obj;

    /**
     * obj list
     *
     * @var self[]
     */
    public array $objList;

}
