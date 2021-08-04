<?php declare(strict_types=1);

namespace Toolkit\Stdlib\Obj\Exception;

use Psr\Container\NotFoundExceptionInterface;
use RuntimeException;

/**
 * Class NotFoundException
 *
 * @package Toolkit\Stdlib\Obj\Exception
 */
class NotFoundException extends RuntimeException implements NotFoundExceptionInterface
{

}
