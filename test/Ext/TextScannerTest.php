<?php declare(strict_types=1);

namespace Toolkit\StdlibTest\Ext;

use Toolkit\Stdlib\Ext\TextScanner;
use Toolkit\StdlibTest\BaseLibTestCase;

/**
 * @author inhere
 */
class TextScannerTest extends BaseLibTestCase
{
    public function testScan_space(): void
    {
        $s = TextScanner::new('hello world abc 123');
        $s->setSplitToken(' '); // split by space

        $ls = [];
        while ($s->scan()) {
            $ls[] = $s->getText();
        }
        $this->assertSame(['hello', 'world', 'abc', '123'], $ls);
    }

    public function testScan_line(): void
    {
        $src = <<<TXT
hello world
abc
name=inhere
desc="some words"
123
TXT;

        $s = TextScanner::new($src);

        $ls = [];
        while ($s->scan()) {
            $ls[] = $s->getText();
        }
        vdump($ls);
        $this->assertNotEmpty($ls);
    }
}
