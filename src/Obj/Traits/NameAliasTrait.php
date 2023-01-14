<?php declare(strict_types=1);
/**
 * This file is part of toolkit/stdlib.
 *
 * @author   https://github.com/inhere
 * @link     https://github.com/php-toolkit/stdlib
 * @license  MIT
 */

namespace Toolkit\Stdlib\Obj\Traits;

use InvalidArgumentException;
use function count;

/**
 * trait NameAliasTrait
 */
trait NameAliasTrait
{
    /**
     * aliases string-map
     *
     * @var array<string, string>
     */
    protected array $aliases = [];

    /**
     * set name alias(es)
     *
     * @param string       $name
     * @param array|string $alias
     * @param bool         $validate
     */
    public function setAlias(string $name, array|string $alias, bool $validate = false): void
    {
        foreach ((array)$alias as $aliasName) {
            if (!isset($this->aliases[$aliasName])) {
                $this->aliases[$aliasName] = $name;
            } elseif ($validate) {
                $oldName = $this->aliases[$aliasName];
                throw new InvalidArgumentException(
                    "Alias '$aliasName' has been registered by '$oldName', cannot assign to the '$name'"
                );
            }
        }
    }

    /**
     * Get real name by alias
     *
     * @param string $alias
     *
     * @return mixed
     */
    public function resolveAlias(string $alias): string
    {
        return $this->aliases[$alias] ?? $alias;
    }

    /**
     * @param string $alias
     *
     * @return bool
     */
    public function hasAlias(string $alias): bool
    {
        return isset($this->aliases[$alias]);
    }

    /**
     * @return int
     */
    public function countAlias(): int
    {
        return count($this->aliases);
    }

    /**
     * get all alias to name map
     *
     * @return array
     */
    public function getAliasMap(): array
    {
        return $this->aliases;
    }

    /**
     * get aliases for input name.
     *
     * @param string $name
     *
     * @return array
     */
    public function getNameAliases(string $name): array
    {
        $aliases = [];
        foreach ($this->aliases as $alias => $n) {
            if ($name === $n) {
                $aliases[] = $alias;
            }
        }

        return $aliases;
    }

    /**
     * get aliases for input name or get all.
     *
     * @param string $name
     *
     * @return array
     */
    public function getAliases(string $name = ''): array
    {
        return $name ? $this->getNameAliases($name) : $this->aliases;
    }
}
