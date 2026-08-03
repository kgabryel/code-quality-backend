<?php

declare(strict_types=1);

namespace Kgabryel\CodeQuality\Tests\DataProvider;

use Kgabryel\CodeQuality\Installer\Enum\Script\TestScript;
use Kgabryel\CodeQuality\Installer\Enum\Tool\TestTool;

class TestScriptDataProvider
{
    public static function applicabilityCases(): array
    {
        return [
            'CodeCoverage dostępny, gdy wybrany PhpUnit' => [TestScript::CodeCoverage, [TestTool::PhpUnit], true],
            'CodeCoverage niedostępny, gdy wybrany tylko Infection' => [
                TestScript::CodeCoverage, [TestTool::Infection], false,
            ],
            'Infection dostępny, gdy wybrany Infection' => [TestScript::Infection, [TestTool::Infection], true],
            'Infection niedostępny, gdy wybrany tylko PhpUnit' => [TestScript::Infection, [TestTool::PhpUnit], false],
            'PhpUnit dostępny, gdy wybrany PhpUnit' => [TestScript::PhpUnit, [TestTool::PhpUnit], true],
            'PhpUnit niedostępny, gdy wybrany tylko Infection' => [TestScript::PhpUnit, [TestTool::Infection], false],
        ];
    }

    public static function scripts(): array
    {
        $cases = [];
        foreach (TestScript::cases() as $script) {
            $cases[$script->name] = [$script];
        }

        return $cases;
    }
}
