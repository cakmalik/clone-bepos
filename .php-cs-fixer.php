<?php

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__)
    ->exclude(['vendor', 'storage', 'bootstrap/cache']);

return (new PhpCsFixer\Config())
    ->setRiskyAllowed(true)
    ->setRules([
        '@PSR12' => true,
        'binary_operator_spaces' => [
            'default' => 'align_single_space_minimal',
        ],
        'array_indentation' => true,
        'align_multiline_comment' => true,
    ])
    ->setFinder($finder);
