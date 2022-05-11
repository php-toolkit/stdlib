<?php declare(strict_types=1);
/**
 * This file is part of toolkit/stdlib.
 *
 * @author   https://github.com/inhere
 * @link     https://github.com/php-toolkit/stdlib
 * @license  MIT
 */

namespace Toolkit\StdlibTest;

use Toolkit\Stdlib\Type;

/**
 * class TypeTest
 *
 * @author inhere
 */
class TypeTest extends BaseLibTestCase
{
    public function testType_fmtValue(): void
    {
        $tests = [
            ['23', Type::INT, 23],
            ['23', Type::INTEGER, 23],
            ['23', 'unknown', '23'],
            ['true', Type::BOOL, true],
            ['yes', Type::BOOL, true],
            ['no', Type::BOOL, false],
            ['true', Type::STRING, 'true'],
            ['no', Type::STRING, 'no'],
        ];

        foreach ($tests as [$value, $type, $want]) {
            $this->assertEquals($want, Type::fmtValue($type, $value));
        }
    }
}
