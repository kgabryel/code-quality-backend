<?php

declare(strict_types=1);

namespace Kgabryel\CodeQuality\Installer\ConfigGenerator;

use Kgabryel\CodeQuality\Installer\ConfigGenerator\Tool\InfectionGenerator;
use Kgabryel\CodeQuality\Installer\Dto\TestOptions;
use Kgabryel\CodeQuality\Installer\Enum\Tool\TestTool;
use Kgabryel\CodeQuality\Installer\Filesystem;

readonly class TestToolGeneratorFactory
{
    public function __construct(private Filesystem $filesystem, private TestOptions $installConfig) {}

    public function build(TestTool $testTool): ?ConfigGenerator
    {
        return match ($testTool) {
            TestTool::PhpUnit => null,
            TestTool::Infection => new InfectionGenerator($this->filesystem, $this->installConfig),
        };
    }
}
