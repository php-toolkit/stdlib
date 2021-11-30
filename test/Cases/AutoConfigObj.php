<?php declare(strict_types=1);

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
