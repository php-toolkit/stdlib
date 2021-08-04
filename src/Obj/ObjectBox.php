<?php declare(strict_types=1);

namespace Toolkit\Stdlib\Obj;

use Psr\Container\ContainerInterface;
use Toolkit\Stdlib\Obj;
use Toolkit\Stdlib\Obj\Exception\ContainerException;
use Toolkit\Stdlib\Obj\Exception\NotFoundException;
use function count;
use function is_array;
use function is_callable;
use function is_object;
use function is_string;
use function method_exists;

/**
 * Class ObjectBox
 *
 * An simple object containers implements
 *
 * @package Toolkit\Stdlib\Obj
 */
class ObjectBox implements ContainerInterface
{
    /**
     * @var array
     */
    private $objects = [];

    /**
     * @var array
     */
    private $definitions = [];

    /**
     * @var self
     */
    private static $global;

    /**
     * @return static
     */
    public static function global(): self
    {
        if (!self::$global) {
            self::$global = new self();
        }

        return self::$global;
    }

    /**
     * @param string $id
     *
     * @return mixed|object
     */
    public function get(string $id)
    {
        if (isset($this->objects[$id])) {
            return $this->objects[$id];
        }

        if (isset($this->definitions[$id])) {
            $obj = $this->createObject($this->definitions[$id]);

            // storage
            $this->objects[$id] = $obj;
            return $obj;
        }

        throw new NotFoundException('box: get undefined object - ' . $id, 404);
    }

    /**
     * @param mixed $value
     *
     * @return mixed
     */
    protected function createObject($value)
    {
        // Closure or has __invoke()
        if (is_object($value) && is_callable($value)) {
            return $value($this);
        }

        // function
        if (is_string($value) && is_callable($value)) {
            return $value($this);
        }

        $obj = null;
        if (is_array($value)) {
            $count = count($value);

            if ($count === 2 && isset($value[0], $value[1]) && is_callable($value)) {
                $obj = $value($this);
            } elseif (isset($value['class'])) {
                $cls = $value['class'];
                $opt = $value['__opts'] ?? [];
                unset($value['class'], $value['__opts']);

                // set construct args, will expand for new object.
                if ($argsForNew = $opt['argsForNew'] ?? []) {
                    $obj = new $cls(...$argsForNew);
                } else {
                    $obj = new $cls();
                }

                // init props
                if ($value) {
                    Obj::init($obj, $value);
                }

                if ($opt) {
                    $init = $opt['init'] ?? true;
                    if ($init && method_exists($obj, 'init')) {
                        $obj->init();
                    }
                }
            }
        }

        // as config data.
        if ($obj === null) {
            $obj = $value;
        }

        return $obj;
    }

    /**
     * @param string $id
     * @param mixed  $definition
     * @param bool   $override
     */
    public function set(string $id, $definition, bool $override = false): void
    {
        if ($override === false && $this->has($id)) {
            throw new ContainerException("box: the '$id' has been registered");
        }

        $this->definitions[$id] = $definition;
    }

    /**
     * Returns true if the container can return an entry for the given identifier.
     * Returns false otherwise.
     *
     * `has($id)` returning true does not mean that `get($id)` will not throw an exception.
     * It does however mean that `get($id)` will not throw a `NotFoundExceptionInterface`.
     *
     * @param string $id Identifier of the entry to look for.
     *
     * @return bool
     */
    public function has(string $id): bool
    {
        if (isset($this->objects[$id])) {
            return true;
        }

        return isset($this->definitions[$id]);
    }

    /**
     * @param string $id
     *
     * @return mixed|null
     */
    public function getObject(string $id)
    {
        return $this->objects[$id] ?? null;
    }

    /**
     * @param string       $id
     * @param object|mixed $obj
     */
    public function setObject(string $id, $obj): void
    {
        $this->objects[$id] = $obj;
    }

    /**
     * @param string $id
     *
     * @return mixed|null
     */
    public function getDefinition(string $id)
    {
        return $this->definitions[$id] ?? null;
    }
}
