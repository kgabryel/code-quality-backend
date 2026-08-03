<?php

declare(strict_types=1);

namespace Kgabryel\CodeQuality\Tests\DataProvider;

use Kgabryel\CodeQuality\Installer\Enum\Script\AnalysisScript;
use Kgabryel\CodeQuality\Installer\Enum\Tool\AnalysisTool;
use Kgabryel\CodeQuality\Installer\Enum\Tool\ComposerTool;

class AnalysisScriptDataProvider
{
    public static function applicabilityCases(): array
    {
        return [
            'CodeCheck dostępny, gdy wybrane dwa z trzech narzędzi' => [
                AnalysisScript::CodeCheck, [AnalysisTool::PhpStan, AnalysisTool::ParallelLint], [], true,
            ],
            'CodeCheck dostępny, gdy wybrane jedno z trzech narzędzi' => [
                AnalysisScript::CodeCheck, [AnalysisTool::PhpStan], [], true,
            ],
            'CodeCheck dostępny, gdy wybrane wszystkie trzy narzędzia' => [
                AnalysisScript::CodeCheck,
                [AnalysisTool::PhpStan, AnalysisTool::Phpcs, AnalysisTool::ParallelLint],
                [],
                true,
            ],
            'CodeCheck niedostępny, gdy żadne z trzech narzędzi nie wybrane' => [
                AnalysisScript::CodeCheck, [AnalysisTool::CsFixer, AnalysisTool::Rector], [], false,
            ],
            'CodeFix dostępny, gdy wybrane dwa z trzech narzędzi' => [
                AnalysisScript::CodeFix, [AnalysisTool::CsFixer, AnalysisTool::Phpcs], [], true,
            ],
            'CodeFix dostępny, gdy wybrane jedno z trzech narzędzi' => [
                AnalysisScript::CodeFix, [AnalysisTool::Rector], [], true,
            ],
            'CodeFix dostępny, gdy wybrane wszystkie trzy narzędzia' => [
                AnalysisScript::CodeFix, [AnalysisTool::CsFixer, AnalysisTool::Phpcs, AnalysisTool::Rector], [], true,
            ],
            'CodeFix niedostępny, gdy żadne z trzech narzędzi nie wybrane' => [
                AnalysisScript::CodeFix, [AnalysisTool::PhpStan, AnalysisTool::ParallelLint], [], false,
            ],
            'ComposerCheck zawsze dostępny' => [AnalysisScript::ComposerCheck, [], [], true],
            'ComposerDeps dostępny, gdy wybrane dwa narzędzia composera' => [
                AnalysisScript::ComposerDeps, [], [ComposerTool::RequireChecker, ComposerTool::Unused], true,
            ],
            'ComposerDeps dostępny, gdy wybrane jedno narzędzie composera' => [
                AnalysisScript::ComposerDeps, [], [ComposerTool::Unused], true,
            ],
            'ComposerDeps dostępny, gdy wybrane wszystkie narzędzia composera' => [
                AnalysisScript::ComposerDeps,
                [],
                [ComposerTool::RequireChecker, ComposerTool::Normalize, ComposerTool::Unused],
                true,
            ],
            'ComposerDeps niedostępny, gdy brak narzędzi composera' => [
                AnalysisScript::ComposerDeps, [], [], false,
            ],
            'CsFixer dostępny, gdy wybrany' => [AnalysisScript::CsFixer, [AnalysisTool::CsFixer], [], true],
            'CsFixer niedostępny, gdy nie wybrany' => [AnalysisScript::CsFixer, [AnalysisTool::PhpStan], [], false],
        ];
    }

    public static function scripts(): array
    {
        $cases = [];
        foreach (AnalysisScript::cases() as $script) {
            $cases[$script->name] = [$script];
        }

        return $cases;
    }
}
