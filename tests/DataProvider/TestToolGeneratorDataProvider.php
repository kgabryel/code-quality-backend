<?php

declare(strict_types=1);

namespace Kgabryel\CodeQuality\Tests\DataProvider;

use Kgabryel\CodeQuality\Installer\ConfigGenerator\Tool\InfectionGenerator;
use Kgabryel\CodeQuality\Installer\Enum\Tool\TestTool;

class TestToolGeneratorDataProvider
{
    public static function tools(): array
    {
        $cases = [];
        foreach (TestTool::cases() as $testTool) {
            $cases[$testTool->name] = [$testTool, self::getExpectedClass($testTool)];
        }

        return $cases;
    }

    private static function getExpectedClass(TestTool $testTool): ?string
    {
        return match ($testTool) {
            TestTool::PhpUnit => null,
            TestTool::Infection => InfectionGenerator::class,
        };
    }
}
