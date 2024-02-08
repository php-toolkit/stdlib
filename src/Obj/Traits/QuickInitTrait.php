<?php declare(strict_types=1);
/**
 * This file is part of toolkit/stdlib.
 *
 * @author   https://github.com/inhere
 * @link     https://github.com/php-toolkit/stdlib
 * @license  MIT
 */

namespace Toolkit\Stdlib\Obj\Traits;

use Toolkit\Stdlib\Json;

/**
 * Trait QuickInitTrait
 *
 */
trait QuickInitTrait
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
     * from JSON string
     *
     * @param ?string $json
     *
     * @return static
     */
    public static function fromJson(?string $json): static
    {
        return new static($json ? Json::decode($json) : []);
    }
}
