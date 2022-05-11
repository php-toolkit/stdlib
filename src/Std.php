<?php declare(strict_types=1);
/**
 * This file is part of toolkit/stdlib.
 *
 * @author   https://github.com/inhere
 * @link     https://github.com/php-toolkit/stdlib
 * @license  MIT
 */

namespace Toolkit\Stdlib;

use Toolkit\Stdlib\Helper\DataHelper;

/**
 * Class Std
 *
 * @package Toolkit\Stdlib
 */
class Std
{
    /**
     * @param mixed $data
     *
     * @return string
     */
    public static function toString(mixed $data): string
    {
        return  DataHelper::toString($data);
    }
}
