<?php declare(strict_types=1);
/**
 * This file is part of toolkit/stdlib.
 *
 * @author   https://github.com/inhere
 * @link     https://github.com/php-toolkit/stdlib
 * @license  MIT
 */

namespace Toolkit\Stdlib\Obj;

use ArrayAccess;
use Closure;
use ReflectionClass;
use ReflectionException;
use ReflectionFunctionAbstract;
use ReflectionMethod;
use ReflectionNamedType;
use RuntimeException;
use Toolkit\Stdlib\Helper\PhpHelper;
use Traversable;
use UnexpectedValueException;
use function base64_decode;
use function base64_encode;
use function basename;
use function dirname;
use function gettype;
use function gzcompress;
use function gzuncompress;
use function is_array;
use function is_numeric;
use function is_object;
use function is_string;
use function iterator_to_array;
use function md5;
use function method_exists;
use function property_exists;
use function serialize;
use function spl_object_hash;
use function sprintf;
use function str_replace;
use function strpos;
use function ucfirst;
use function unserialize;

/**
 * Class ObjectHelper
 *
 * @package Toolkit\Stdlib\Obj
 */
class ObjectHelper
{
    public const TO_ARRAY_METHOD = 'toArray';

    /**
     * 给对象设置属性值
     * - 会先尝试用 setter 方法设置属性
     * - 再尝试直接设置属性
     *
     * @param object|mixed $object An object instance
     * @param array $options
     *
     * @return mixed
     */
    public static function init($object, array $options)
    {
        foreach ($options as $property => $value) {
            if (is_numeric($property)) {
                continue;
            }

            $setter = 'set' . ucfirst($property);

            // has setter
            if (method_exists($object, $setter)) {
                $object->$setter($value);
            } elseif (property_exists($object, $property)) {
                $object->$property = $value;
            }
        }

        return $object;
    }

    /**
     * 给对象设置属性值
     *
     * @param       $object
     * @param array $options
     */
    public static function configure($object, array $options): void
    {
        foreach ($options as $property => $value) {
            if (property_exists($object, $property)) {
                $object->$property = $value;
            }
        }
    }

    /**
     * 给对象设置属性值
     *
     * @param object|mixed  $object
     * @param array $options
     */
    public static function setAttrs($object, array $options): void
    {
        self::configure($object, $options);
    }

    /**
     * @param object|mixed $object
     * @param array $data
     *
     * @throws ReflectionException
     */
    public static function mappingProps($object, array $data): void
    {
        $rftObj = PhpHelper::reflectClass($object);
        foreach ($rftObj->getProperties() as $rftProp) {
            // TODO
            // $typeName = $rftProp->getType()
        }
    }

    /**
     * 定义一个用来序列化数据的函数
     *
     * @param mixed $obj
     *
     * @return string
     */
    public static function encode($obj): string
    {
        return base64_encode(gzcompress(serialize($obj)));
    }

    /**
     * 反序列化
     *
     * @param string     $txt
     * @param bool|array $allowedClasses
     *
     * @return mixed
     */
    public static function decode(string $txt, $allowedClasses = false)
    {
        return unserialize(gzuncompress(base64_decode($txt)), ['allowed_classes' => $allowedClasses]);
    }

    /**
     * php对象转换成为数组
     *
     * @param iterable|array|Traversable $data
     * @param bool                       $recursive
     *
     * @return array|bool
     */
    public static function toArray($data, bool $recursive = false)
    {
        $arr = [];

        // Ensure the input data is an array.
        if (is_object($data)) {
            if ($data instanceof Traversable) {
                $arr = iterator_to_array($data);
            } elseif (method_exists($data, 'toArray')) {
                $arr = $data->toArray();
            }
        } else {
            $arr = (array)$data;
        }

        if ($recursive) {
            foreach ($arr as $key => $value) {
                if (is_array($value) || is_object($value)) {
                    $arr[$key] = static::toArray($value, $recursive);
                }
            }
        }

        return $arr;
    }

    /**
     * @param object|mixed $object
     *
     * @return bool
     */
    public static function isArrayable($object): bool
    {
        return $object instanceof ArrayAccess || method_exists($object, 'toArray');
    }

    /**
     * @param mixed $object
     * @param bool $unique
     *
     * @return string
     */
    public static function hash($object, bool $unique = true): string
    {
        if (is_object($object)) {
            $hash = spl_object_hash($object);

            if ($unique) {
                $hash = md5($hash);
            }

            return $hash;
        }

        // a class
        return is_string($object) ? md5($object) : '';
    }

    /**
     * Build an array of class method parameters.
     *
     * @param ReflectionFunctionAbstract $rftFunc
     * @param array                      $provideArgs
     *
     * @psalm-param array<string, mixed> $provideArgs
     *
     * @return array
     * @throws ReflectionException
     */
    public static function buildReflectCallArgs(ReflectionFunctionAbstract $rftFunc, array $provideArgs = []): array
    {
        $funcArgs = [];
        foreach ($rftFunc->getParameters() as $param) {
            $name = $param->getName();
            $pType = $param->getType();
            if (!$pType instanceof ReflectionNamedType) {
                if ($param->isOptional()) {
                    $funcArgs[] = $param->getDefaultValue();
                    continue;
                }

                throw new RuntimeException(sprintf(
                    'Could not resolve the %dth parameter(%s)',
                    $param->getPosition(),
                    $name
                ));
            }

            // filling by param type. eg: an class name
            $typeName = $pType->getName();
            if ($typeName !== Closure::class && isset($provideArgs[$typeName])) {
                $funcArgs[] = $provideArgs[$typeName];
                continue;
            }

            // filling by param name and type is same.
            if (isset($provideArgs[$name]) && $typeName === gettype($provideArgs[$name])) {
                $funcArgs[] = $provideArgs[$name];
                continue;
            }

            // Finally, if there is a default parameter, use it.
            if ($param->isOptional()) {
                $funcArgs[] = $param->getDefaultValue();
                continue;
            }

            throw new RuntimeException(sprintf(
                'Could not resolve the %dth parameter(%s)',
                $param->getPosition(),
                $name
            ));
        }

        return $funcArgs;
    }

    /**
     * 从类名创建服务实例对象，会尽可能自动补完构造函数依赖
     *
     * @from windWalker https://github.com/ventoviro/windwalker
     *
     * @param string $class a className
     *
     * @return mixed
     * @throws ReflectionException
     */
    public static function create(string $class)
    {
        try {
            $reflection = new ReflectionClass($class);
        } catch (ReflectionException $e) {
            return false;
        }

        $constructor = $reflection->getConstructor();

        // If there are no parameters, just return a new object.
        if (null === $constructor) {
            return new $class;
        }

        $newInstanceArgs = self::getMethodArgs($constructor);

        // Create a callable for the dataStorage
        return $reflection->newInstanceArgs($newInstanceArgs);
    }

    /**
     * @param string|array $config
     *
     * @return mixed
     */
    public static function createByArray($config)
    {
        // is class name.
        if (is_string($config)) {
            return new $config;
        }

        if (is_array($config) && !empty($config['class'])) {
            $class = $config['class'];
            $args  = $config[0] ?? [];

            $obj = new $class(...$args);

            unset($config['class'], $config[0]);
            return self::init($obj, $config);
        }

        return null;
    }

    /**
     * Build an array of class method parameters.
     *
     * @param ReflectionMethod $method      Method for which to build the argument array.
     * @param array            $provideArgs Manual provide params map.
     *
     * @return array
     * @throws RuntimeException
     * @throws ReflectionException
     */
    public static function getMethodArgs(ReflectionMethod $method, array $provideArgs = []): array
    {
        $methodArgs = [];

        foreach ($method->getParameters() as $idx => $param) {
            // if user have been provide arg
            if (isset($provideArgs[$idx])) {
                $methodArgs[] = $provideArgs[$idx];
                continue;
            }

            // $depRftClass = $param->getClass();
            $depRftClass = $param->getType();

            // If we have a dependency, that means it has been type-hinted.
            if ($depRftClass && ($depClass = $depRftClass->getName()) !== Closure::class) {
                $depObject = self::create($depClass);

                if ($depObject instanceof $depClass) {
                    $methodArgs[] = $depObject;
                    continue;
                }
            }

            // Finally, if there is a default parameter, use it.
            if ($param->isOptional()) {
                $methodArgs[] = $param->getDefaultValue();
                continue;
            }

            // $dependencyVarName = $param->getName();
            // Couldn't resolve dependency, and no default was provided.
            throw new RuntimeException(sprintf(
                'Could not resolve dependency: %s for the %dth parameter',
                $param->getName(),
                $param->getPosition()
            ));
        }

        return $methodArgs;
    }

    /**
     * Get class namespace
     *
     * @param string $fullClass
     *
     * @return string
     */
    public static function spaceName(string $fullClass): string
    {
        $fullClass = str_replace('\\', '/', $fullClass);

        return strpos($fullClass, '/') ? dirname($fullClass) : '';
    }

    /**
     * Get class name without namespace.
     *
     * @param string $fullClass
     *
     * @return string
     */
    public static function className(string $fullClass): string
    {
        $fullClass = str_replace('\\', '/', $fullClass);

        return basename($fullClass);
    }

    /**
     * @param object $obj
     * @param string $errMsg
     *
     * @return object
     */
    public static function requireNotNull($obj, string $errMsg = '')
    {
        if ($obj === null) {
            throw new UnexpectedValueException($errMsg ?: 'object must not be null');
        }

        return $obj;
    }
}
