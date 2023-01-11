<?php declare(strict_types=1);
/**
 * This file is part of toolkit/stdlib.
 *
 * @author   https://github.com/inhere
 * @link     https://github.com/php-toolkit/stdlib
 * @license  MIT
 */

namespace Toolkit\Stdlib\Obj;

use Toolkit\Stdlib\Obj;
use Toolkit\Stdlib\Obj\Traits\QuickInitTrait;

/**
 * Class AbstractObj
 *
 * @package Toolkit\Stdlib\Obj
 */
abstract class AbstractObj
{
    use QuickInitTrait;

    /**
     * Class constructor.
     *
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        Obj::init($this, $config);
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return Obj::toArray($this, false, false);
    }
}
