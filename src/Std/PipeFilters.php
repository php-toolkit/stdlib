<?php declare(strict_types=1);

namespace Toolkit\Stdlib\Std;

use Toolkit\Stdlib\Helper\Assert;
use Toolkit\Stdlib\Obj\AbstractObj;
use Toolkit\Stdlib\Obj\Traits\NameAliasTrait;
use Toolkit\Stdlib\Str;
use function array_shift;
use function is_array;
use function is_string;
use function str_contains;
use function strlen;

/**
 * class PipeFilters
 *
 * @author inhere
 * @date 2023/1/14
 */
class PipeFilters extends AbstractObj
{
    use NameAliasTrait;

    /**
     * @var bool
     */
    public bool $allowPhpFunc = true;

    /**
     * @var bool
     */
    public bool $stopOnEmpty = true;

    /**
     * @var bool
     */
    public bool $trimForRule = true;

    /**
     * sep char for split filter name and args
     *
     * @var string
     */
    protected string $nameSep = ':';

    /**
     * sep char for split args on filter rules
     *
     * @var string
     */
    protected string $argsSep = ',';

    /**
     * custom filters
     *
     * @var array<string, callable>
     */
    protected array $filters = [];

    /**
     * @return static
     */
    public static function newWithDefaultFilters(): self
    {
        return self::new()->loadDefaultFilters();
    }

    /**
     * @return array<string, callable>
     */
    public static function getDefaultFilters(): array
    {
        return [
            'upper' => 'strtoupper',
            'lower' => 'strtolower',
        ];
    }

    /**
     * Alias for applyStringRules()
     *
     * ## Usage
     *
     * ```php
     * $pf = PipeFilters::newWithDefaultFilters();
     *
     * $val = $pf->applyString('inhere', 'upper'); // 'INHERE'
     * $val = $pf->applyString('inhere', 'upper | substr:0,3'); // 'INH'
     * ```
     *
     * @param mixed $value
     * @param string $rules
     * @param string $sep Sep char for multi filter rules
     *
     * @return mixed
     */
    public function applyString(mixed $value, string $rules, string $sep = '|'): mixed
    {
        return $this->applyRules($value, Str::explode($rules, $sep));
    }

    /**
     * @param mixed $value
     * @param string $rules
     * @param string $sep Sep char for multi filter rules
     *
     * @return mixed
     */
    public function applyStringRules(mixed $value, string $rules, string $sep = '|'): mixed
    {
        return $this->applyRules($value, Str::explode($rules, $sep));
    }

    /**
     * Alias for applyRules()
     *
     * @param mixed $value
     * @param array $filterRules
     *
     * @return mixed
     */
    public function apply(mixed $value, array $filterRules): mixed
    {
        return $this->applyRules($value, $filterRules);
    }

    /**
     * Apply filters for value
     *
     * ## Usage
     *
     * ```php
     * $pf = PipeFilters::newWithDefaultFilters();
     *
     * $val = $pf->apply('inhere', ['upper']); // 'INHERE'
     * $val = $pf->apply('inhere', ['upper', 'substr:0,3']); // 'INH'
     * ```
     *
     * @param mixed $value
     * @param array $filterRules filter names can be with args
     *
     * @return mixed
     */
    public function applyRules(mixed $value, array $filterRules): mixed
    {
        foreach ($filterRules as $filter) {
            if ($this->stopOnEmpty && !$value) {
                break;
            }

            $isStr = is_string($filter);
            $args = [];

            // eg 'wrap:,'
            if ($isStr && str_contains($filter, $this->nameSep)) {
                [$filter, $argStr] = Str::toTrimmedArray($filter, $this->nameSep, 2);
                if (strlen($argStr) > 1 && str_contains($argStr, $this->argsSep)) {
                    $args = Str::toTypedList($argStr, $this->argsSep);
                } else {
                    $args = [Str::toTyped($argStr)];
                }
            }

            $filterFn = $filter;
            if ($isStr) {
                $filter = $this->resolveAlias($filter);
                if (isset($this->filters[$filter] )) {
                    $filterFn = $this->filters[$filter];
                }

                // filter func and with args: ['my_func', arg1, arg2]
            } elseif (is_array($filter) && isset($filter[1])) {
                $filterFn = array_shift($filter);
                // remain as args
                $args = $filter;
            }

            // call filter func
            if ($args) {
                $value = $filterFn($value, ...$args);
            } else {
                $value = $filterFn($value);
            }
        }

        return $value;
    }

    /**
     * @return $this
     */
    public function loadDefaultFilters(): self
    {
        return $this->addFilters(self::getDefaultFilters());
    }

    /**
     * @param array<string, callable> $filters
     *
     * @return $this
     */
    public function addFilters(array $filters): self
    {
        foreach ($filters as $name => $filterFn) {
            $this->addFilter($name, $filterFn);
        }
        return $this;
    }

    /**
     * @param string $name
     * @param callable $filterFn
     *
     * @return $this
     */
    public function addFilter(string $name, callable $filterFn): self
    {
        Assert::notEmpty($name, 'filter name cannot be empty');
        $this->filters[$name] = $filterFn;
        return $this;
    }

    /**
     * @return bool
     */
    public function isAllowPhpFunc(): bool
    {
        return $this->allowPhpFunc;
    }

    /**
     * @param bool $allowPhpFunc
     */
    public function setAllowPhpFunc(bool $allowPhpFunc): void
    {
        $this->allowPhpFunc = $allowPhpFunc;
    }

    /**
     * @return string
     */
    public function getNameSep(): string
    {
        return $this->nameSep;
    }

    /**
     * @param string $nameSep
     */
    public function setNameSep(string $nameSep): void
    {
        if ($nameSep) {
            Assert::isTrue(strlen($nameSep) === 1, 'filter name args sep must be one char');
            $this->nameSep = $nameSep;
        }
    }

    /**
     * @return string
     */
    public function getArgsSep(): string
    {
        return $this->argsSep;
    }

    /**
     * @param string $argsSep
     */
    public function setArgsSep(string $argsSep): void
    {
        if ($argsSep) {
            Assert::isTrue(strlen($argsSep) === 1, 'filter name args sep must be one char');
            $this->argsSep = $argsSep;
        }
    }
}
