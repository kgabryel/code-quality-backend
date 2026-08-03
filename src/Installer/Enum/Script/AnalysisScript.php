<?php

declare(strict_types=1);

namespace Kgabryel\CodeQuality\Installer\Enum\Script;

use Kgabryel\CodeQuality\Installer\Enum\Tool\AnalysisTool;
use Kgabryel\CodeQuality\Installer\Enum\Tool\ComposerTool;

enum AnalysisScript: string implements Script
{
    case CodeCheck = 'Check code';
    case CodeFix = 'Reformat code';
    case ComposerCheck = 'composer check';
    case ComposerDeps = 'Composer - dependency analysis';
    case ComposerNormalize = 'composer normalize';
    case ComposerRequireChecker = 'composer-require-checker';
    case ComposerUnused = 'composer-unused';
    case CsFixer = 'PHP CS Fixer';
    case ParallelLint = 'PHP Parallel Lint';
    case PhpcsCheck = 'PHP Code Sniffer (check)';
    case PhpcsFix = 'PHP Code Sniffer (fix)';
    case PhpStan = 'PHPStan';
    case Rector = 'Rector';

    /**
     * @param array<AnalysisTool> $analysisTools
     * @param array<ComposerTool> $composerTools
     *
     * @return list<string>
     */
    public static function getAvailable(array $analysisTools, array $composerTools): array
    {
        $activeCases = array_filter(
            self::cases(),
            static fn (self $script): bool => $script->isApplicable($analysisTools, $composerTools)
        );

        return array_column($activeCases, 'value');
    }

    public function getFileName(): string
    {
        return match ($this) {
            self::CsFixer => 'cs-fixer',
            self::PhpcsCheck => 'phpcs',
            self::PhpcsFix => 'phpcbf',
            self::PhpStan => 'phpstan',
            self::Rector => 'rector',
            self::ParallelLint => 'parallel-lint',
            self::ComposerRequireChecker => 'composer-require-checker',
            self::ComposerUnused => 'composer-unused',
            self::ComposerNormalize => 'composer-normalize',
            self::ComposerCheck => 'composer-check',
            self::CodeFix => 'reformat',
            self::CodeCheck => 'check',
            self::ComposerDeps => 'composer-deps',
        };
    }

    /**
     * @param array<AnalysisTool> $analysisTools
     * @param array<ComposerTool> $composerTools
     */
    public function isApplicable(array $analysisTools, array $composerTools): bool
    {
        return match ($this) {
            self::CsFixer => in_array(AnalysisTool::CsFixer, $analysisTools, true),
            self::PhpcsCheck, self::PhpcsFix => in_array(AnalysisTool::Phpcs, $analysisTools, true),
            self::PhpStan => in_array(AnalysisTool::PhpStan, $analysisTools, true),
            self::Rector => in_array(AnalysisTool::Rector, $analysisTools, true),
            self::ParallelLint => in_array(AnalysisTool::ParallelLint, $analysisTools, true),
            self::ComposerRequireChecker => in_array(ComposerTool::RequireChecker, $composerTools, true),
            self::ComposerUnused => in_array(ComposerTool::Unused, $composerTools, true),
            self::ComposerNormalize => in_array(ComposerTool::Normalize, $composerTools, true),
            self::ComposerCheck => true,
            self::CodeFix => array_any(
                [AnalysisTool::CsFixer, AnalysisTool::Phpcs, AnalysisTool::Rector],
                static fn (AnalysisTool $tool): bool => in_array($tool, $analysisTools, true)
            ),
            self::CodeCheck => array_any(
                [AnalysisTool::PhpStan, AnalysisTool::Phpcs, AnalysisTool::ParallelLint],
                static fn (AnalysisTool $tool): bool => in_array($tool, $analysisTools, true)
            ),
            self::ComposerDeps => array_any(
                [ComposerTool::RequireChecker, ComposerTool::Normalize, ComposerTool::Unused],
                static fn (ComposerTool $tool): bool => in_array($tool, $composerTools, true)
            ),
        };
    }
}
