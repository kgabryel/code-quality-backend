<?php

declare(strict_types=1);

namespace Kgabryel\CodeQuality\Tests\DataProvider;

use Kgabryel\CodeQuality\Installer\ConfigGenerator\Tool\CsFixerGenerator;
use Kgabryel\CodeQuality\Installer\ConfigGenerator\Tool\PhpCsGenerator;
use Kgabryel\CodeQuality\Installer\ConfigGenerator\Tool\PhpStanGenerator;
use Kgabryel\CodeQuality\Installer\ConfigGenerator\Tool\RectorGenerator;
use Kgabryel\CodeQuality\Installer\Enum\Tool\AnalysisTool;

class AnalysisToolGeneratorDataProvider
{
    public static function tools(): array
    {
        $cases = [];
        foreach (AnalysisTool::cases() as $analysisTool) {
            $cases[$analysisTool->name] = [$analysisTool, self::getExpectedClass($analysisTool)];
        }

        return $cases;
    }

    private static function getExpectedClass(AnalysisTool $analysisTool): ?string
    {
        return match ($analysisTool) {
            AnalysisTool::Rector => RectorGenerator::class,
            AnalysisTool::CsFixer => CsFixerGenerator::class,
            AnalysisTool::PhpStan => PhpStanGenerator::class,
            AnalysisTool::Phpcs => PhpCsGenerator::class,
            AnalysisTool::ParallelLint => null,
        };
    }
}
