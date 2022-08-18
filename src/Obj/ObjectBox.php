<?php declare(strict_types=1);
/**
 * This file is part of toolkit/stdlib.
 *
 * @author   https://github.com/inhere
 * @link     https://github.com/php-toolkit/stdlib
 * @license  MIT
 */

namespace Toolkit\Stdlib\Obj;

use Psr\Container\ContainerInterface;
use Toolkit\Stdlib\Obj;
use Toolkit\Stdlib\Obj\Exception\ContainerException;
use Toolkit\Stdlib\Obj\Exception\NotFoundException;
use Toolkit\Stdlib\Obj\Traits\AutoConfigTrait;
use function array_keys;
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
 * NOTICE: require the `psr/container` package.
 *
 * @package Toolkit\Stdlib\Obj
 */
class ObjectBox implements ContainerInterface
{
    use AutoConfigTrait;

    /**
     * only create object on first fetch.
     */
    public const TYPE_SINGLETON = 1;

    /**
     * will always create new object.
     */
    public const TYPE_PROTOTYPE = 2;

    /**
     * @var self|null
     */
    private static ?ObjectBox $global = null;

    /**
     * Whether call init method {@see $initMethod} on object create.
     *
     * @var bool
     */
    private bool $callInit = true;

    /**
     * @var string
     */
    private string $initMethod = 'init';

    /**
     * Created objects
     *
     * @var array
     */
    private array $objects = [];

    /**
     * Definitions for create objects
     *
     * @var array
     */
    private array $definitions = [];

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
     * Get an object by registered ID name
     *
     * @param string $id
     *
     * @return mixed
     */
    public function get(string $id): mixed
    {
        // has created.
        if (isset($this->objects[$id])) {
            return $this->objects[$id];
        }

        // create from definition
        if (isset($this->definitions[$id])) {
            [$obj, $opt] = $this->createObject($this->definitions[$id]);

            if (is_object($obj)) {
                $callInit = $opt['callInit'] ?? $this->callInit;

                // has init method in the object, call it.
                if ($callInit && method_exists($obj, $this->initMethod)) {
                    $obj->init();
                }

                // storage it on type is TYPE_SINGLETON
                if ($opt['objType'] ?? self::TYPE_SINGLETON) {
                    $this->objects[$id] = $obj;
                }
            }

            // type TYPE_PROTOTYPE always create new object.
            return $obj;
        }

        throw new NotFoundException('box: get undefined object - ' . $id, 404);
    }

    /**
     * Create object from definition.
     *
     * return options:
     *
     * ```php
     * [
     *  callInit => bool,
     *  objType  => int,
     * ]
     * ```
     *
     * @param mixed $value The definition value.
     *
     * @return array{mixed, array}
     */
    protected function createObject(mixed $value): array
    {
        $opt = [];

        // Closure or has __invoke()
        if (is_object($value) && is_callable($value)) {
            return [$value($this), $opt];
        }

        // function name
        if (is_string($value) && is_callable($value)) {
            return [$value($this), $opt];
        }

        $obj = null;
        if (is_array($value)) {
            $count = count($value);

            // like [class, method]
            if ($count === 2 && isset($value[0], $value[1]) && is_callable($value)) {
                $obj = $value($this);
            } elseif (isset($value['class'])) { // full config
                $cls = $value['class'];
                $opt = $value['__opt'] ?? [];
                unset($value['class'], $value['__opt']);

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
            } elseif (isset($value['__creator']) && is_callable($value['__creator'])) {
                $creator = $value['__creator'];
                // create object.
                $obj = $creator($this);
                $opt = $value['__opt'] ?? [];
            }
        }

        // as config data.
        if ($obj === null) {
            $obj = $value;
        }

        return [$obj, $opt];
    }

    /**
     * Register an service definition to the box.
     *
     * **For $definition**:
     *
     * - Closure(ObjectBox): object
     * - Object and has __invoke()
     * - string: an function name
     * - array: callable array [class, method]
     * - array: config array
     *
     * ```php
     * [
     *  'class' => class-string,
     *  // '__creator' => callable(ObjectBox): object, // can also use creator func.
     *
     *  // option for create object.
     *  '__opt' => [
     *      'callInit'   => true,
     *      'objType'    => ObjectBox::TYPE_PROTOTYPE,
     *      'argsForNew' => [$arg0, $arg1],
     *  ],
     *  // props settings ...
     *  'propName' => value,
     * ]
     * ```
     *
     * - more, any type as config data.
     *
     * @param string $id
     * @param mixed  $definition
     * @param bool   $override
     */
    public function set(string $id, mixed $definition, bool $override = false): void
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
     * @return mixed
     */
    public function getObject(string $id): mixed
    {
        return $this->objects[$id] ?? null;
    }

    /**
     * @param string       $id
     * @param object|mixed $obj
     */
    public function setObject(string $id, mixed $obj): void
    {
        $this->objects[$id] = $obj;
    }

    /**
     * @param string $id
     *
     * @return mixed
     */
    public function getDefinition(string $id): mixed
    {
        return $this->definitions[$id] ?? null;
    }

    /**
     * @return array
     */
    public function getDefinitions(): array
    {
        return $this->definitions;
    }

    /**
     * @return array
     */
    public function getObjectIds(): array
    {
        return array_keys($this->definitions);
    }

    /**
     * @return bool
     */
    public function isCallInit(): bool
    {
        return $this->callInit;
    }

    /**
     * @param bool $callInit
     */
    public function setCallInit(bool $callInit): void
    {
        $this->callInit = $callInit;
    }

    /**
     * @return string
     */
    public function getInitMethod(): string
    {
        return $this->initMethod;
    }

    /**
     * @param string $initMethod
     */
    public function setInitMethod(string $initMethod): void
    {
        if ($initMethod) {
            $this->initMethod = $initMethod;
        }
    }
}
