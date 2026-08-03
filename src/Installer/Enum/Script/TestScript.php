<?php

declare(strict_types=1);

namespace Kgabryel\CodeQuality\Installer\Enum\Script;

use Kgabryel\CodeQuality\Installer\Enum\Tool\TestTool;

enum TestScript: string implements Script
{
    case CodeCoverage = 'PHPUnit (coverage report)';
    case Infection = 'Infection';
    case PhpUnit = 'PhpUnit';

    public function getFileName(): string
    {
        return match ($this) {
            self::CodeCoverage => 'code-coverage',
            self::PhpUnit => 'phpunit',
            self::Infection => 'infection',
        };
    }

    /**
     * @param array<TestTool> $tools
     */
    public function isApplicable(array $tools): bool
    {
        return match ($this) {
            self::CodeCoverage, self::PhpUnit => in_array(TestTool::PhpUnit, $tools, true),
            self::Infection => in_array(TestTool::Infection, $tools, true),
        };
    }

    public function needsCoverage(): bool
    {
        return match ($this) {
            self::PhpUnit => false,
            self::CodeCoverage, self::Infection => true,
        };
    }

    public function order(): int
    {
        return match ($this) {
            self::PhpUnit => 0,
            self::CodeCoverage => 1,
            self::Infection => 2,
        };
    }

    /**
     * @param array<TestTool> $tools
     *
     * @return list<string>
     */
    public static function values(array $tools): array
    {
        $applicable = array_filter(
            self::cases(),
            static fn (self $script): bool => $script->isApplicable($tools),
        );

        usort($applicable, static fn (self $a, self $b): int => $a->order() <=> $b->order());

        return array_values(array_map(
            static fn (self $script): string => $script->value,
            $applicable,
        ));
    }
}
