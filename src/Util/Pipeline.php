<?php declare(strict_types=1);
/**
 * This file is part of toolkit/stdlib.
 *
 * @author   https://github.com/inhere
 * @link     https://github.com/php-toolkit/stdlib
 * @license  MIT
 */

namespace Toolkit\Stdlib\Util;

use SplObjectStorage;
use Toolkit\Stdlib\Util\Contract\PipelineInterface;
use function is_callable;

/**
 * Class Pipeline
 *
 * @package Toolkit\Util
 * @link    https://github.com/ztsu/pipe/blob/master/src/Pipeline.php
 */
class Pipeline implements PipelineInterface
{
    /**
     * @var SplObjectStorage
     */
    private $stages;

    public function __construct()
    {
        $this->stages = new SplObjectStorage;
    }

    /**
     * {@inheritdoc}
     */
    public function add(callable $stage): PipelineInterface
    {
        if ($stage instanceof $this) {
            $stage->add(function ($payload) {
                return $this->invokeStage($payload);
            });
        }

        $this->stages->attach($stage);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function run($payload)
    {
        $this->stages->rewind();

        return $this->invokeStage($payload);
    }

    /**
     * {@inheritdoc}
     */
    public function __invoke($payload)
    {
        return $this->run($payload);
    }

    private function invokeStage($payload)
    {
        $stage = $this->stages->current();
        $this->stages->next();

        if (is_callable($stage)) {
            return $stage($payload, function ($payload) {
                return $this->invokeStage($payload);
            });
        }

        return $payload;
    }
}
