<?php declare(strict_types=1);
/**
 * This file is part of toolkit/stdlib.
 *
 * @author   https://github.com/inhere
 * @link     https://github.com/php-toolkit/stdlib
 * @license  MIT
 */

namespace Toolkit\StdlibTest\Cases;

use Toolkit\Stdlib\Obj\Traits\AutoConfigTrait;

/**
 * class AutoConfigObj
 *
 * @author inhere
 */
class AutoConfigObj
{
    use AutoConfigTrait;

    public int $age = 0;

    private string $name = '';

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
}
