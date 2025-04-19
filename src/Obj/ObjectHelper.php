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
use Toolkit\Stdlib\Str\StringHelper;
use Traversable;
use UnexpectedValueException;
use function base64_decode;
use function base64_encode;
use function basename;
use function dirname;
use function get_object_vars;
use function gettype;
use function gzcompress;
use function gzuncompress;
use function is_array;
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
 */
class ObjectHelper
{
    public const TO_ARRAY_METHOD = 'toArray';

    /**
     * 给对象设置属性值
     * - 会先尝试用 setter 方法设置属性
     * - 再尝试直接设置属性
     *
     * @param object $obj An object instance
     * @param array $data
     * @param bool $toCamel
     * @param array $aliasMap
     *
     * @return object
     */
    public static function init(object $obj, array $data, bool $toCamel = false, array $aliasMap = []): object
    {
        foreach ($data as $field => $value) {
            if ($aliasMap) {
                $field = $aliasMap[$field] ?? $field;
            }

            if ($toCamel) {
                $field = StringHelper::toCamel($field);
            }

            self::setValue($obj, $field, $value);
        }

        return $obj;
    }

    /**
     * Set property value for object. first, will try to use setter method.
     *
     * @param object $obj
     * @param string $field
     * @param mixed $value
     *
     * @return void
     */
    public static function setValue(object $obj, string $field, mixed $value): void
    {
        // check has setter
        $setter = 'set' . ucfirst($field);
        if (method_exists($obj, $setter)) {
            $obj->$setter($value);
        } elseif (property_exists($obj, $field)) {
            $obj->$field = $value;
        }
    }

    /**
     * Set property values for object. first, will try to use setter method.
     *
     * @param object $obj
     * @param array $data
     */
    public static function configure(object $obj, array $data): void
    {
        foreach ($data as $field => $value) {
           self::setValue($obj, $field, $value);
        }
    }

    /**
     * Set property values for object
     *
     * @param object $object
     * @param array $options
     */
    public static function setAttrs(object $object, array $options): void
    {
        self::configure($object, $options);
    }

    /**
     * Copy src object properties value to dst object
     *
     * @param object $srcObj
     * @param object $dstObj
     */
    public static function copyProps(object $srcObj, object $dstObj): void
    {
        self::configure($dstObj, self::toArray($srcObj));
    }

    /**
     * 定义一个用来序列化数据的函数
     *
     * @param mixed $obj
     *
     * @return string
     */
    public static function encode(mixed $obj): string
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
    public static function decode(string $txt, bool|array $allowedClasses): mixed
    {
        return unserialize(gzuncompress(base64_decode($txt)), ['allowed_classes' => $allowedClasses]);
    }

    /**
     * Convert PHP object to array map, key is property name, value is property value.
     *
     * @param object $obj
     * @param bool $recursive
     * @param bool $checkMth in the data obj
     *
     * @return array
     */
    public static function toArray(object $obj, bool $recursive = false, bool $checkMth = true): array
    {
        if ($obj instanceof Traversable) {
            $arr = iterator_to_array($obj);
        } elseif ($checkMth && method_exists($obj, self::TO_ARRAY_METHOD)) {
            $arr = $obj->toArray();
        } else {
            $arr = get_object_vars($obj);
        }

        if ($recursive) {
            foreach ($arr as $key => $value) {
                if (is_object($value)) {
                    $arr[$key] = static::toArray($value, $recursive, $checkMth);
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
    public static function isArrayable(mixed $object): bool
    {
        return $object instanceof ArrayAccess || method_exists($object, self::TO_ARRAY_METHOD);
    }

    /**
     * @param mixed $object
     * @param bool $unique
     *
     * @return string
     */
    public static function hash(mixed $object, bool $unique = true): string
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
    public static function create(string $class): mixed
    {
        try {
            $reflection = new ReflectionClass($class);
        } catch (ReflectionException $_) {
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
     * @param array|string $config
     *
     * @return mixed
     */
    public static function createByArray(array|string $config): mixed
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
            if ($depRftClass && $depRftClass instanceof ReflectionNamedType && ($depClass = $depRftClass->getName()) !== Closure::class) {
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
     * @param mixed $obj
     * @param string $errMsg
     *
     * @return object
     */
    public static function requireNotNull(mixed $obj, string $errMsg = ''): object
    {
        if ($obj === null) {
            throw new UnexpectedValueException($errMsg ?: 'object must not be null');
        }

        if (!is_object($obj)) {
            throw new UnexpectedValueException($errMsg ?: 'Expected a non-null object');
        }

        return $obj;
    }
}
