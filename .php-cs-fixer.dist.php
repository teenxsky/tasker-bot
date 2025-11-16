<?php

declare(strict_types=1);

use PhpCsFixer\Config;
use PhpCsFixer\Finder;
use PhpCsFixer\Runner\Parallel\ParallelConfigFactory;

$finder = Finder::create()
    ->in(['app', 'config', 'routes'])
    ->exclude(['vendor'])
    ->name('*.php');

return new Config()
    ->setParallelConfig(ParallelConfigFactory::detect())
    ->setFinder($finder)
    ->setRiskyAllowed(true)
    ->setUsingCache(true)
    ->setRules([
        '@PSR12'                              => true,
        'no_unused_imports'                   => true,
        'ordered_imports'                     => ['sort_algorithm' => 'alpha'],
        'no_extra_blank_lines'                => true,
        'line_ending'                         => true,
        'single_quote'                        => true,
        'strict_param'                        => true,
        'final_class'                         => true,
        'no_useless_else'                     => true,
        'no_useless_return'                   => true,
        'whitespace_after_comma_in_array'     => true,
        'unary_operator_spaces'               => true,
        'phpdoc_align'                        => true,
        'phpdoc_annotation_without_dot'       => true,
        'phpdoc_scalar'                       => true,
        'phpdoc_types'                        => true,
        'phpdoc_var_annotation_correct_order' => true,
        'phpdoc_var_without_name'             => true,
        'php_unit_method_casing'              => true,
        'phpdoc_types_order'                  => [
            'null_adjustment' => 'always_last'
        ],
        'binary_operator_spaces' => [
            'default'   => 'align_single_space_minimal',
            'operators' => [
                '='   => 'align_single_space_minimal',
                '=>'  => 'align_single_space_minimal',
                '??=' => 'align_single_space_minimal',
            ],
        ],
        'declare_strict_types'    => true,
        'global_namespace_import' => [
            'import_classes'   => true,
            'import_constants' => true,
            'import_functions' => true,
        ],
    ]);
