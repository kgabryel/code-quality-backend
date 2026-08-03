<?php

declare(strict_types=1);

namespace Kgabryel\CodeQuality\Installer\Cli\Analysis;

use Kgabryel\CodeQuality\Installer\Cli\Input\BaseScriptsCollector;
use Kgabryel\CodeQuality\Installer\Dto\ScriptsOptions;
use Kgabryel\CodeQuality\Installer\Enum\Project\Environment;
use Kgabryel\CodeQuality\Installer\Enum\Script\AnalysisScript;
use Kgabryel\CodeQuality\Installer\Enum\Text\Label;
use Kgabryel\CodeQuality\Installer\Enum\Text\Message;
use Kgabryel\CodeQuality\Installer\Enum\Tool\AnalysisTool;
use Kgabryel\CodeQuality\Installer\Enum\Tool\ComposerTool;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\multiselect;

readonly class AnalysisScriptsCollector extends BaseScriptsCollector
{
    /**
     * @param array<AnalysisTool> $analysisTools
     * @param array<ComposerTool> $composerTools
     *
     * @return null|ScriptsOptions<AnalysisScript>
     */
    public function collect(array $analysisTools, array $composerTools): ?ScriptsOptions
    {
        $generateScripts = confirm(label: Label::GenerateScripts->value);

        if (!$generateScripts) {
            return null;
        }

        $environment = $this->detectEnvironment();

        return new ScriptsOptions(
            $environment,
            $this->selectScripts($analysisTools, $composerTools),
            $environment === Environment::Docker ? $this->selectContainerName() : '',
        );
    }

    /**
     * @param array<AnalysisTool> $analysisTools
     * @param array<ComposerTool> $composerTools
     *
     * @return array<AnalysisScript>
     */
    private function selectScripts(array $analysisTools, array $composerTools): array
    {
        $selected = multiselect(
            label: Label::Scripts->value,
            options: AnalysisScript::getAvailable($analysisTools, $composerTools),
            default: AnalysisScript::getAvailable($analysisTools, $composerTools),
            hint: Message::MultiselectHint->value,
        );

        return array_map(
            static fn (int|string $value) => AnalysisScript::from((string) $value),
            $selected
        );
    }
}
