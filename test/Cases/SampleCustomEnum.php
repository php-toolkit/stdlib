<?php declare(strict_types=1);
/**
 * This file is part of toolkit/stdlib.
 *
 * @author   https://github.com/inhere
 * @link     https://github.com/php-toolkit/stdlib
 * @license  MIT
 */

namespace Toolkit\StdlibTest\Cases;

use Toolkit\Stdlib\Std\BaseEnum;

/**
 * class SimpleEnum
 *
 * @author inhere
 */
class SampleCustomEnum extends BaseEnum
{
    public const ADD = [1, 'add'];

    public const VIEW = [2, 'view'];

    public const EDIT = [3, 'edit'];

    public int $code;

    public string $msg;
}
