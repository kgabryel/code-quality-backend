<?php

declare(strict_types=1);

namespace Kgabryel\CodeQuality\Installer\Script;

use Kgabryel\CodeQuality\Installer\ConfigGenerator\FrameworkPaths;
use Kgabryel\CodeQuality\Installer\Enum\Script\Script;
use Kgabryel\CodeQuality\Installer\Enum\Script\TestScript;

/** @implements ScriptCommand<TestScript> */
readonly class TestScriptCommand implements ScriptCommand
{
    private const string COVERAGE_DIR = 'code-coverage';

    public function __construct(private FrameworkPaths $frameworkPaths) {}

    public function coveragePath(): string
    {
        return $this->frameworkPaths->getCacheDir() . self::COVERAGE_DIR;
    }

    public function get(Script $script): string
    {
        return match ($script) {
            TestScript::PhpUnit => 'php -d memory_limit=512M vendor/bin/phpunit',
            TestScript::CodeCoverage => sprintf(
                'XDEBUG_MODE=coverage php -d memory_limit=512M vendor/bin/phpunit --coverage-html %s',
                $this->coveragePath(),
            ),
            TestScript::Infection => sprintf(
                'XDEBUG_MODE=coverage php -d memory_limit=%1$s vendor/bin/infection --initial-tests-php-options="-d memory_limit=%1$s"',
                '1024M',
            ),
        };
    }
}
