<?php declare(strict_types=1);

namespace Toolkit\StdlibTest\Std;

use Toolkit\Stdlib\Std\PipeFilters;
use Toolkit\StdlibTest\BaseLibTestCase;
use function strtoupper;

/**
 * class PipeFiltersTest
 *
 * @author inhere
 * @date 2023/1/14
 */
class PipeFiltersTest extends BaseLibTestCase
{
    public function testApplyRules(): void
    {
        $pf = PipeFilters::new()->loadDefaultFilters();
        $pf->setAlias('upper', 'up');

        $value = 'inhere';

        $wanted = 'INHERE';
        $this->assertEquals($wanted, $pf->applyRules($value, ['up']));

        $wanted = 'INH';
        $this->assertEquals($wanted, $pf->applyRules($value, ['upper', 'substr:0, 3']));

        $this->assertEquals($wanted, $pf->applyRules($value, [function($val) {
            return strtoupper(substr($val, 0, 3));
        }]));
    }

    public function testApplyStringRules(): void
    {
        $tests = [
            ['INHERE', 'inhere', 'upper'],
            ['INH', 'inhere', 'upper | substr:0,3'],
        ];

        $pf = PipeFilters::newWithDefaultFilters();
        foreach ($tests as [$wanted, $value, $rules]) {
            $this->assertEquals($wanted, $pf->applyString($value, $rules));
        }
    }
}
