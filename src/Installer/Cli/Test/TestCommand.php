<?php

declare(strict_types=1);

namespace Kgabryel\CodeQuality\Installer\Cli\Test;

use Kgabryel\CodeQuality\Installer\Cli\Command;
use Kgabryel\CodeQuality\Installer\ConfigGenerator\ConfigGenerator;
use Kgabryel\CodeQuality\Installer\ConfigGenerator\ConfigWriter;
use Kgabryel\CodeQuality\Installer\ConfigGenerator\TestToolGeneratorFactory;
use Kgabryel\CodeQuality\Installer\Dto\TestOptions;
use Kgabryel\CodeQuality\Installer\Enum\Project\CoverageDriver;
use Kgabryel\CodeQuality\Installer\Enum\Script\TestScript;
use Kgabryel\CodeQuality\Installer\Enum\Text\Message;
use Kgabryel\CodeQuality\Installer\Filesystem;
use Kgabryel\CodeQuality\Installer\FileWriter;
use Kgabryel\CodeQuality\Installer\ProcessRunner;
use Kgabryel\CodeQuality\Installer\Script\TestScriptCommand;

use function Laravel\Prompts\info;
use function Laravel\Prompts\warning;

readonly class TestCommand extends Command
{
    private Filesystem $filesystem;

    private TestInputCollector $inputCollector;

    public function __construct(
        ProcessRunner $processRunner,
        FileWriter $fileWriter,
        ConfigWriter $configWriter,
        TestInputCollector $inputCollector,
        Filesystem $filesystem,
    ) {
        parent::__construct($processRunner, $fileWriter, $configWriter);
        $this->inputCollector = $inputCollector;
        $this->filesystem = $filesystem;
    }

    public function run(): void
    {
        $config = $this->inputCollector->collect();

        if ($this->warnIfNothingToDo($config)) {
            return;
        }

        $this->installDependencies($config);
        $this->generateConfigs($config);
        $this->generateScripts($config->scriptsConfig, new TestScriptCommand($config->getFrameworkPaths()));

        $this->warnAboutCoverageDriver($config);
        info(Message::Done->value);
    }

    private function generateConfigs(TestOptions $config): void
    {
        $factory = new TestToolGeneratorFactory($this->filesystem, $config);

        $generators = array_values(array_filter(
            array_map($factory->build(...), $config->tools),
            static fn (?ConfigGenerator $generator): bool => $generator instanceof ConfigGenerator,
        ));

        if ($generators === []) {
            return;
        }

        $this->configWriter->write(
            $generators,
            $this->fileWriter,
            $config->getFrameworkPaths()
        );
    }

    private function warnAboutCoverageDriver(TestOptions $config): void
    {
        $scripts = $config->scriptsConfig?->scripts ?? [];
        $anyNeedsCoverage = array_any($scripts, static fn (TestScript $script): bool => $script->needsCoverage());

        if (!$anyNeedsCoverage || CoverageDriver::detect() !== CoverageDriver::None) {
            return;
        }

        warning(Message::NoCoverageDriver->value);
    }
}
