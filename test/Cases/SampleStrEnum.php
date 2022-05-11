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
class SampleStrEnum extends BaseEnum
{
    public const ADD = 'add';

    public const VIEW = 'view';

    public const EDIT = 'edit';
}
