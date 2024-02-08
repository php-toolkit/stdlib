<?php declare(strict_types=1);
/**
 * This file is part of toolkit/stdlib.
 *
 * @author   https://github.com/inhere
 * @link     https://github.com/php-toolkit/stdlib
 * @license  MIT
 */

namespace Toolkit\Stdlib\Util\Contract;

use Closure;

/**
 * Interface PipelineInterface
 *
 */
interface PipelineInterface
{
    /**
     * Adds stage to the pipeline
     *
     * @param Closure $stage
     *
     * @return $this
     */
    public function add(Closure $stage): PipelineInterface;

    /**
     * Runs pipeline with initial value
     *
     * @param mixed $payload
     *
     * @return mixed
     */
    public function run(mixed $payload): mixed;

    /**
     * Makes pipeline callable. Does same as {@see run()}
     *
     * @param mixed $payload
     *
     * @return mixed
     */
    public function __invoke(mixed $payload): mixed;
}
