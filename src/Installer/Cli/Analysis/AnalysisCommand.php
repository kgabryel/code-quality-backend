<?php

declare(strict_types=1);

namespace Kgabryel\CodeQuality\Installer\Cli\Analysis;

use Kgabryel\CodeQuality\Installer\Cli\Command;
use Kgabryel\CodeQuality\Installer\ConfigGenerator\AnalysisToolGeneratorFactory;
use Kgabryel\CodeQuality\Installer\ConfigGenerator\ConfigGenerator;
use Kgabryel\CodeQuality\Installer\ConfigGenerator\ConfigWriter;
use Kgabryel\CodeQuality\Installer\Dto\AnalysisOptions;
use Kgabryel\CodeQuality\Installer\Enum\Text\Message;
use Kgabryel\CodeQuality\Installer\Filesystem;
use Kgabryel\CodeQuality\Installer\FileWriter;
use Kgabryel\CodeQuality\Installer\ProcessRunner;
use Kgabryel\CodeQuality\Installer\Script\AnalysisScriptCommand;

use function Laravel\Prompts\info;

readonly class AnalysisCommand extends Command
{
    private Filesystem $filesystem;

    private AnalysisInputCollector $inputCollector;

    public function __construct(
        ProcessRunner $processRunner,
        FileWriter $fileWriter,
        ConfigWriter $configWriter,
        AnalysisInputCollector $inputCollector,
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
        $this->generateScripts($config->scriptsConfig, new AnalysisScriptCommand($config));

        info(Message::Done->value);
    }

    private function generateConfigs(AnalysisOptions $config): void
    {
        $factory = new AnalysisToolGeneratorFactory($this->filesystem, $config);

        $generators = array_values(array_filter(
            array_map($factory->build(...), $config->analysisTools),
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
}
