<?php declare(strict_types=1);

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
    public static function toString($data): string
    {
        return  DataHelper::toString($data);
    }
}
