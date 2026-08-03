<?php

declare(strict_types=1);

namespace Kgabryel\CodeQuality\Tests\DataProvider;

use Kgabryel\CodeQuality\Installer\Enum\Tool\AnalysisTool;
use Kgabryel\CodeQuality\Installer\Enum\Tool\ComposerTool;

class AnalysisOptionsToolsDataProvider
{
    public static function tools(): array
    {
        return [
            'brak narzędzi' => [
                'analysisTools' => [],
                'composerTools' => [],
                'expected' => [],
            ],
            'oba typy narzędzi' => [
                'analysisTools' => [AnalysisTool::CsFixer, AnalysisTool::PhpStan],
                'composerTools' => [ComposerTool::Normalize],
                'expected' => [AnalysisTool::CsFixer, AnalysisTool::PhpStan, ComposerTool::Normalize],
            ],
            'tylko analysisTools' => [
                'analysisTools' => [AnalysisTool::CsFixer, AnalysisTool::PhpStan],
                'composerTools' => [],
                'expected' => [AnalysisTool::CsFixer, AnalysisTool::PhpStan],
            ],
            'tylko composerTools' => [
                'analysisTools' => [],
                'composerTools' => [ComposerTool::Normalize],
                'expected' => [ComposerTool::Normalize],
            ],
        ];
    }
}
