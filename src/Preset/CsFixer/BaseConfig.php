<?php

declare(strict_types=1);

namespace Kgabryel\CodeQuality\Preset\CsFixer;

readonly class BaseConfig
{
    /** @return list<string> */
    public static function getPaths(string $baseDir): array
    {
        return [
            $baseDir . '/src',
            $baseDir . '/tests',
        ];
    }

    /** @return array<string, mixed> */
    public static function getSets(): array
    {
        return [
            '@PhpCsFixer' => true,
            'braces_position' => [
                'classes_opening_brace' => 'next_line_unless_newline_at_signature_end',
                'functions_opening_brace' => 'next_line_unless_newline_at_signature_end',
            ],
            'concat_space' => ['spacing' => 'one'],
            'declare_strict_types' => true,
            'global_namespace_import' => ['import_classes' => true],
            'multiline_whitespace_before_semicolons' => [
                'strategy' => 'no_multi_line',
            ],
            'ordered_attributes' => [
                'order' => [
                    'PHPUnit\Framework\Attributes\Small',
                    'PHPUnit\Framework\Attributes\Medium',
                    'PHPUnit\Framework\Attributes\Large',
                    'PHPUnit\Framework\Attributes\CoversClass',
                    'PHPUnit\Framework\Attributes\Test',
                    'PHPUnit\Framework\Attributes\TestDox',
                    'PHPUnit\Framework\Attributes\DataProvider',
                    'PHPUnit\Framework\Attributes\DataProviderExternal',
                ],
                'sort_algorithm' => 'custom',
            ],
            'ordered_class_elements' => [
                'order' => [
                    'use_trait',
                    'case',
                    'constant_public',
                    'constant_protected',
                    'constant_private',
                    'property_public',
                    'property_protected',
                    'property_private',
                    'construct',
                    'destruct',
                    'magic',
                    'phpunit',
                    'method_public',
                    'method_protected',
                    'method_private',
                ],
                'sort_algorithm' => 'alpha',
            ],
            'phpdoc_align' => ['align' => 'left'],
            'strict_comparison' => true,
            'strict_param' => true,
            'yoda_style' => false,
        ];
    }

    public static function isRiskyAllowed(): bool
    {
        return true;
    }
}
