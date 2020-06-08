<?php

$header = <<<'EOF'
This file is part of toolkit/stdlib.

@author   https://github.com/inhere
@link     https://github.com/php-toolkit/stdlib
@license  MIT
EOF;

return PhpCsFixer\Config::create()
    ->setRiskyAllowed(true)
    ->setRules([
        '@PSR2' => true,
        'array_syntax' => [
            'syntax' => 'short'
        ],
        'list_syntax' => [
            'syntax' => 'short'
        ],
        'class_attributes_separation' => true,
        'declare_strict_types' => true,
        'global_namespace_import' => [
            'import_constants' => true,
            'import_functions' => true,
        ],
        'header_comment' => [
            'comment_type' => 'PHPDoc',
            'header'    => $header,
            'separate'  => 'bottom'
        ],
        'no_unused_imports' => true,
        'single_quote' => true,
        'standardize_not_equals' => true,
    ])
    ->setFinder(
        PhpCsFixer\Finder::create()
            // ->exclude('test')
            ->exclude('runtime')
            ->exclude('vendor')
            ->in(__DIR__)
    )
    ->setUsingCache(false);
