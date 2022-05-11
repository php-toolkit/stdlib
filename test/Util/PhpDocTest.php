<?php declare(strict_types=1);
/**
 * This file is part of toolkit/stdlib.
 *
 * @author   https://github.com/inhere
 * @link     https://github.com/php-toolkit/stdlib
 * @license  MIT
 */

namespace Toolkit\StdlibTest\Util;

use PHPUnit\Framework\TestCase;
use ReflectionMethod;
use Toolkit\Stdlib\Util\PhpDoc;

/**
 * class PhpDocTest
 */
class PhpDocTest extends TestCase
{
    /**
     * Open an github repository by browser
     *
     * @options
     *  -r, --remote         The git remote name. default is `origin`
     *      --main           Use the config `mainRemote` name
     *
     * @arguments
     *  repoPath    The remote git repo URL or repository group/name.
     *              If not input, will auto parse from current work directory
     *
     * @param string $input
     * @param int $output
     *
     * @example
     *  {fullCmd}  php-toolkit/cli-utils
     *  {fullCmd}  https://github.com/php-toolkit/cli-utils
     */
    public function testDoc_basic(): void
    {
        $rftMth = new ReflectionMethod($this, 'testDoc_basic');

        $tags = PhpDoc::getTags($rftMth->getDocComment(), [
            'default' => 'desc',
        ]);

        $this->assertArrayHasKey('desc', $tags);
        $this->assertArrayHasKey('example', $tags);
        $this->assertArrayHasKey('options', $tags);
        $this->assertArrayHasKey('arguments', $tags);

        $this->assertEquals('Open an github repository by browser', $tags['desc']);
    }
}
