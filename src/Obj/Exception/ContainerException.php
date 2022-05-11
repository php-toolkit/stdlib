<?php declare(strict_types=1);
/**
 * This file is part of toolkit/stdlib.
 *
 * @author   https://github.com/inhere
 * @link     https://github.com/php-toolkit/stdlib
 * @license  MIT
 */

namespace Toolkit\Stdlib\Obj\Exception;

use Psr\Container\ContainerExceptionInterface;
use RuntimeException;

/**
 * Class ContainerException
 *
 * @package Toolkit\Stdlib\Obj\Exception
 */
class ContainerException extends RuntimeException implements ContainerExceptionInterface
{
}
