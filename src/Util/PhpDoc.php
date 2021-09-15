<?php declare(strict_types=1);
/**
 * This file is part of toolkit/stdlib.
 *
 * @author   https://github.com/inhere
 * @link     https://github.com/php-toolkit/stdlib
 * @license  MIT
 */

namespace Toolkit\Stdlib\Util;

use function array_merge;
use function in_array;
use function preg_match;
use function preg_replace;
use function preg_split;
use function str_replace;
use function substr;
use function trim;
use const PREG_OFFSET_CAPTURE;
use const PREG_SPLIT_NO_EMPTY;

/**
 * Class PhpDoc
 *
 * @package Toolkit\PhpKit
 */
class PhpDoc
{
    /**
     * @param string $doc
     * @param array $options
     *
     * @return array
     */
    public static function getTags(string $doc, array $options = []): array
    {
        return self::parseDocs($doc, $options);
    }

    /**
     * 以下三个方法来自 yii2 console/Controller.php 做了一些调整
     */

    /**
     * Parses the comment block into tags.
     *
     * @param string $comment The comment block text
     * @param array $options
     *                        - 'allow'  // only allowed tags
     *                        - 'multi'  // allowed multi tag names
     *                        - 'ignore' // ignored tags
     *                        - 'default' => 'description', // default tag name, first line text will attach to it.
     *
     * @psalm-param array{allow: array, multi: array, ignore: array, default: string} $options
     *
     * @return array The parsed tags
     */
    public static function parseDocs(string $comment, array $options = []): array
    {
        if (!$comment = trim($comment, "/ \n")) {
            return [];
        }

        $options = array_merge([
            'allow'   => [], // only allowed tags
            'multi'   => [], // allowed multi tags
            'ignore'  => ['param', 'return'], // ignored tags
            'default' => 'description', // default tag name, first line text will attach to it.
        ], $options);

        $multi   = (array)$options['multi'];
        $allowed = (array)$options['allow'];
        $ignored = (array)$options['ignore'];
        $default = (string)$options['default'];

        $comment = str_replace("\r\n", "\n", $comment);
        $comment = "@$default \n" . str_replace(
                "\r",
                '',
                trim(preg_replace('/^\s*\**( |\t)?/m', '', $comment))
            );

        $tags  = [];
        $parts = preg_split('/^\s*@/m', $comment, -1, PREG_SPLIT_NO_EMPTY);

        foreach ($parts as $part) {
            if (preg_match('/^(\w+)(.*)/ms', trim($part), $matches)) {
                $name = $matches[1];
                if (!$name || in_array($name, $ignored, true)) {
                    continue;
                }

                // always allow default tag
                if ($default !== $name && $allowed && !in_array($name, $allowed, true)) {
                    continue;
                }

                // allow multi tag
                if ($multi && in_array($name, $multi, true)) {
                    $tags[$name][] = trim($matches[2]);
                } else {
                    $tags[$name] = trim($matches[2]);
                }
            }
        }

        return $tags;
    }

    /**
     * Returns the first line of docBlock.
     *
     * @param string $comment
     *
     * @return string
     */
    public static function firstLine(string $comment): string
    {
        $docLines = preg_split('~\R~u', $comment);

        if (isset($docLines[1])) {
            return trim($docLines[1], "/\t *");
        }

        return '';
    }

    /**
     * Returns full description from the doc-block.
     * If have multi line text, will return multi line.
     *
     * @param string $comment
     *
     * @return string
     */
    public static function description(string $comment): string
    {
        $comment = str_replace("\r", '', trim(preg_replace('/^\s*\**( |\t)?/m', '', trim($comment, '/'))));

        if (preg_match('/^\s*@\w+/m', $comment, $matches, PREG_OFFSET_CAPTURE)) {
            $comment = trim(substr($comment, 0, $matches[0][1]));
        }

        return $comment;
    }
}
