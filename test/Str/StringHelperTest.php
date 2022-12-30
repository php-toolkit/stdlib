<?php declare(strict_types=1);
/**
 * This file is part of toolkit/stdlib.
 *
 * @author   https://github.com/inhere
 * @link     https://github.com/php-toolkit/stdlib
 * @license  MIT
 */

namespace Toolkit\StdlibTest\Str;

use PHPUnit\Framework\TestCase;
use Toolkit\Stdlib\Str;

/**
 * Class StringHelperTest
 *
 * @package Toolkit\StdlibTest\Str
 */
class StringHelperTest extends TestCase
{
    public function testBasicStrMethods(): void
    {
        $this->assertEquals('', Str::wrap('', '"'));
        $this->assertEquals('"a"', Str::wrap('a', '"'));
        $this->assertEquals(['"a"', '"b"'], Str::wrapList(['a', 'b'], '"'));

        $this->assertTrue(Str::isNull('null'));
        $this->assertFalse(Str::isNull('abc'));

        $this->assertTrue(Str::isAlphaNum('abc'));
        $this->assertTrue(Str::isAlphaNum('abc23'));
        $this->assertFalse(Str::isAlphaNum('--'));
    }

    public function testIsBool(): void
    {
        $this->assertTrue(Str::isBool('true'));
        $this->assertTrue(Str::isBool('false'));
        $this->assertFalse(Str::isBool('abc'));
    }

    public function testIsVarName(): void
    {
        $this->assertTrue(Str::isVarName('true'));
        $this->assertTrue(Str::isVarName('abc'));
        $this->assertTrue(Str::isVarName('some_name'));
        $this->assertFalse(Str::isVarName('some-name'));
        $this->assertFalse(Str::isVarName('some_name()'));
    }

    public function testToTyped(): void
    {
        $tests = [
            ['abc', 'abc'],
            ['true', true],
            ['23', 23],
            ['23.4', 23.4],
        ];
        foreach ($tests as [$in, $out]) {
            $this->assertEquals($out, Str::toTyped($in, true));
        }

        $this->assertEquals('true', Str::toTyped('true'));
    }

    public function testParamQuotes(): void
    {
        $tests = [
            ['', "''"],
            ['abc', "'abc'"],
            ["'abc'", "'abc'"],
            ['ab" c', "'ab\" c'"],
            ["ab' c", '"ab\' c"'],
        ];
        foreach ($tests as [$given, $want]) {
            self::assertSame($want, Str::paramQuotes($given));
        }
    }

    public function testShellQuote(): void
    {
        $tests = [
            ['', '""'],
            ['abc', 'abc'],
            ['ab c', '"ab c"'],
            ['ab"c', "'ab\"c'"],
        ];

        foreach ($tests as [$given, $want]) {
            $this->assertEquals($want, Str::shellQuote($given));
        }
    }

    public function testStrLen(): void
    {
        $tests = [
            ['abc', 3],
            [123, 3],
            [123.4, 5],
            ['23ab', 4],
        ];

        foreach ($tests as [$case, $want]) {
            $this->assertSame($want, Str::strlen($case));
        }
    }

    public function testHasPrefix(): void
    {
        self::assertTrue(Str::hasPrefix('abc', 'a'));
        self::assertFalse(Str::hasPrefix('abc', 'c'));
        self::assertTrue(Str::endWiths('abc', 'c'));
        self::assertTrue(Str::hasSuffix('abc', 'bc'));
        self::assertFalse(Str::hasSuffix('abc', 'b'));
    }

    public function testIEndWiths(): void
    {
        self::assertTrue(Str::endWithIC('abC', 'C'));
        self::assertTrue(Str::endWithIC('abC', 'c'));
        self::assertFalse(Str::endWithIC('abc', 'b'));

        self::assertTrue(Str::hasPrefixIC('abc', 'a'));
        self::assertTrue(Str::hasPrefixIC('abc', 'A'));
        self::assertFalse(Str::hasPrefixIC('abc', 'b'));
    }

    public function testHasPrefixIC(): void
    {
        $tests = [
            ['abc', 'a', true],
            ['23ab', 'a', false],
        ];

        foreach ($tests as [$case, $want, $yes]) {
            if ($yes) {
                $this->assertTrue(Str::hasPrefixIC($case, $want));
                $this->assertTrue(Str::startWithIC($case, $want));
                $this->assertTrue(Str::isStartWithIC($case, $want));
            } else {
                $this->assertFalse(Str::hasPrefixIC($case, $want));
            }
        }
    }

    public function testStrpos(): void
    {
        $tests = [
            ['abc', 'a'],
            ['23ab', 'a'],
        ];

        foreach ($tests as [$case, $want]) {
            $this->assertTrue(Str::has($case, $want));
            $this->assertTrue(Str::contains($case, $want));
            $this->assertTrue(Str::ihas($case, $want));
            $this->assertTrue(Str::icontains($case, $want));
        }

        self::assertTrue(Str::notContains('abc', 'd'));
        self::assertFalse(Str::notContains('abc', 'b'));
    }

    public function testStrCase_toCamel(): void
    {
        $tests = [
            ['voicePlayTimes', 'voicePlayTimes', 'VoicePlayTimes'],
            ['fieldName', 'fieldName', 'FieldName'],
            ['the_fieldName', 'theFieldName', 'TheFieldName'],
            ['the_field_name', 'theFieldName', 'TheFieldName'],
            ['the-field-name', 'theFieldName', 'TheFieldName'],
            ['the field name', 'theFieldName', 'TheFieldName'],
        ];

        foreach ($tests as [$case, $want, $want1]) {
            $this->assertEquals(Str::toCamel($case), $want);
            $this->assertEquals(Str::toCamel($case, true), $want1);
        }
    }

    public function testToNoEmptyArray(): void
    {
        $tests = [
            ['ab, cd', ',', ['ab', 'cd']],
            ['ab / cd', '/', ['ab', 'cd']],
            ['ab | cd', '|', ['ab', 'cd']],
            [' fieldName   some  desc', ' ', ['fieldName', 'some', 'desc']],
            [' ab   0  cd ', ' ', ['ab', '0', 'cd']],
        ];

        foreach ($tests as [$given, $sep, $want]) {
            $this->assertEquals($want, Str::toNoEmptyArray($given, $sep));
        }
    }

    public function testToArray_no_limit(): void
    {
        $tests = [
            ['34,56,678, 678, 89, ', ',', ['34', '56', '678', '678', '89']],
            [' fieldName   some  desc', ' ', ['fieldName', 'some', 'desc']],
            [' ab   0  cd ', ' ', ['ab', '0', 'cd']],
        ];

        foreach ($tests as [$given, $sep, $want]) {
            $this->assertEquals($want, Str::toNoEmptyArray($given, $sep));
            $this->assertEquals($want, Str::splitTrimFiltered($given, $sep));
        }
    }

    public function testToArray_limit(): void
    {
        $tests = [
            [
                ' fieldName   some  desc msg ',
                ' ',
                2,
                ['fieldName', 'some  desc msg']
            ],
            [
                ' ab   0  cd ',
                ' ',
                2,
                ['ab', '0  cd']
            ],
        ];

        foreach ($tests as [$given, $sep, $limit, $want]) {
            $this->assertEquals($want, Str::splitTrimFiltered($given, $sep, $limit));
            $this->assertEquals($want, Str::toNoEmptyArray($given, $sep, $limit));
        }
    }

    /**
     * TIP: recommend use Str::toNoEmptyArray()
     */
    public function testDiff_splitTrimFiltered_toNoEmptyArray(): void
    {
        $str = ' fieldName,,   some,  desc, msg ';
        $this->assertEquals(
            ['fieldName', 'some',  'desc, msg'], // better
            Str::toNoEmptyArray($str, ',', 3)
        );
        $this->assertEquals(
            ['fieldName', 'some,  desc, msg'], // only two elem
            Str::splitTrimFiltered($str, ',', 3)
        );

        $sep = ' ';
        $str = ' fieldName   some  desc msg ';
        $this->assertEquals(
            ['fieldName', 'some',  'desc msg'],  // better
            Str::toNoEmptyArray($str, $sep, 3)
        );
        $this->assertEquals(
            ['fieldName', 'some  desc msg'], // only two elem
            Str::splitTrimFiltered($str, $sep, 3)
        );
    }

    public function testSplitTypedList(): void
    {
        $tests = [
            ['34,56,678, 678, 89, ', [34, 56, 678, 678, 89]],
            ['a,,34, 3.4 ', ['a', 34, 3.4]],
            ['ab,,true ', ['ab', true]],
        ];

        foreach ($tests as [$given, $want]) {
            $this->assertEquals($want, Str::splitTypedList($given));
            $this->assertEquals($want, Str::toTypedArray($given));
        }
    }

    public function testRenderVars(): void
    {
        $vars = [
            'name' => 'inhere',
            'age'  => 200,
            'tags' => ['php', 'java'],
            'top' => [
                'key0' => 'val0',
            ]
        ];

        $text = Str::renderVars('hello {{ name }}, age: {{ age}}, tags: {{ tags.0 }}, key0: {{top.key0 }}', $vars);
        $this->assertEquals('hello inhere, age: 200, tags: php, key0: val0', $text);

        $text = Str::renderVars('hello ${ name }, age: ${ age}, tags: ${ tags.0 }, key0: ${top.key0 }', $vars, '${%s}');
        $this->assertEquals('hello inhere, age: 200, tags: php, key0: val0', $text);

        $text = Str::renderVars('tags: ${ tags }', $vars, '${%s}');
        $this->assertEquals('tags: [php, java]', $text);
    }
}
