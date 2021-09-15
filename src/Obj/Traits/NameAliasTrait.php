<?php declare(strict_types=1);

namespace Toolkit\Stdlib\Obj\Traits;

use InvalidArgumentException;
use function count;

/**
 * trait NameAliasTrait
 */
trait NameAliasTrait
{
    /**
     * @var array
     */
    private $aliases = [];

    /**
     * set name alias(es)
     *
     * @param string       $name
     * @param string|array $alias
     * @param bool         $validate
     */
    public function setAlias(string $name, $alias, bool $validate = false): void
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
     * @param string $name
     *
     * @return array
     */
    public function getAliases(string $name = ''): array
    {
        if ($name) {
            $aliases = [];
            foreach ($this->aliases as $alias => $n) {
                if ($name === $n) {
                    $aliases[] = $alias;
                }
            }

            return $aliases;
        }

        return $this->aliases;
    }
}
