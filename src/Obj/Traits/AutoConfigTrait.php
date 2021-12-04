<?php declare(strict_types=1);

namespace Toolkit\Stdlib\Obj\Traits;

use Toolkit\Stdlib\Obj;

/**
 * trait AutoConfigTrait
 *
 * ## Usage:
 *
 * ```php
 * class MyClass {
 *  use AutoConfigTrait;
 * }
 * ```
 *
 * - Want call __construct:
 *
 * ```php
 * class MyClass {
 *  use AutoConfigTrait{
 *      __construct as supper;
 *  }
 *
 *  public function __construct(array $config = [])
 *  {
 *      $this->supper($config);
 *
 *      // do something
 *  }
 * }
 * ```
 *
 * @author inhere
 */
trait AutoConfigTrait
{
    /**
     * @param array $config
     *
     * @return static
     */
    public static function new(array $config = []): static
    {
        return new static($config);
    }

    /**
     * Class constructor.
     *
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        Obj::init($this, $config);
    }
}
