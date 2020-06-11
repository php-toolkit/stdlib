<?php declare(strict_types=1);
/**
 * This file is part of toolkit/stdlib.
 *
 * @author   https://github.com/inhere
 * @link     https://github.com/php-toolkit/stdlib
 * @license  MIT
 */

namespace Toolkit\Stdlib\Obj\Traits;

use Toolkit\Stdlib\Obj\Exception\GetPropertyException;
use Toolkit\Stdlib\Obj\Exception\PropertyException;
use Toolkit\Stdlib\Obj\Exception\SetPropertyException;
use function get_class;
use function method_exists;

/**
 * trait PropertyAccessByGetterSetterTrait
 *
 * @package Toolkit\Stdlib\Obj\Traits
 *
 * ```
 * class A
 * {
 *     use PropertyAccessByGetterSetterTrait;
 * }
 * ```
 */
trait PropertyAccessByGetterSetterTrait
{
    /**
     * @reference yii2 yii\base\Object::__set()
     *
     * @param $name
     * @param $value
     *
     * @throws SetPropertyException
     */
    public function __set($name, $value): void
    {
        $setter = 'set' . ucfirst($name);

        if (method_exists($this, $setter)) {
            $this->$setter($value);
        } elseif (method_exists($this, 'get' . ucfirst($name))) {
            throw new SetPropertyException('Setting a Read-only property! ' . get_class($this) . "::{$name}");
        } else {
            throw new SetPropertyException('Setting a Unknown property! ' . get_class($this) . "::{$name}");
        }
    }

    /**
     * @reference yii2 yii\base\Object::__set()
     *
     * @param $name
     *
     * @return mixed
     * @throws GetPropertyException
     */
    public function __get($name)
    {
        $getter = 'get' . ucfirst($name);

        if (method_exists($this, $getter)) {
            return $this->$getter();
        }

        if (method_exists($this, 'set' . ucfirst($name))) {
            throw new GetPropertyException('Getting a Write-only property! ' . get_class($this) . "::{$name}");
        }

        throw new GetPropertyException('Getting a Unknown property! ' . get_class($this) . "::{$name}");
    }

    /**
     * @param $name
     *
     * @return bool
     */
    public function __isset($name)
    {
        $getter = 'get' . ucfirst($name);

        if (method_exists($this, $getter)) {
            return $this->$getter() !== null;
        }

        return false;
    }

    /**
     * @param $name
     *
     * @throws PropertyException
     */
    public function __unset($name): void
    {
        $setter = 'set' . ucfirst($name);

        if (method_exists($this, $setter)) {
            $this->$setter(null);
            return;
        }

        throw new PropertyException('Unset an unknown or read-only property: ' . get_class($this) . '::' . $name);
    }
}
