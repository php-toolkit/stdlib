<?php declare(strict_types=1);
/**
 * This file is part of toolkit/stdlib.
 *
 * @author   https://github.com/inhere
 * @link     https://github.com/php-toolkit/stdlib
 * @license  MIT
 */

namespace Toolkit\Stdlib\Obj;

use Toolkit\Stdlib\Obj\Traits\PropertyAccessByGetterSetterTrait;

/**
 * Class Configurable
 *
 * @package Toolkit\Stdlib\Obj
 */
abstract class Configurable
{
    use PropertyAccessByGetterSetterTrait;

    /**
     * Configurable constructor.
     *
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        if ($config) {
            ObjectHelper::init($this, $config);
        }

        $this->init();
    }

    /**
     * init
     */
    protected function init(): void
    {
        // init something ...
    }
}
