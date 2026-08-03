<?php

declare(strict_types=1);

namespace Kgabryel\CodeQuality\Installer\Cli\Analysis;

use Kgabryel\CodeQuality\Installer\Cli\Input\BaseInputCollector;
use Kgabryel\CodeQuality\Installer\Dto\AnalysisOptions;
use Kgabryel\CodeQuality\Installer\Enum\Project\PhpVersion;
use Kgabryel\CodeQuality\Installer\Enum\Text\Label;
use Kgabryel\CodeQuality\Installer\Enum\Text\Message;
use Kgabryel\CodeQuality\Installer\Enum\Tool\AnalysisTool;
use Kgabryel\CodeQuality\Installer\Enum\Tool\ComposerTool;

use function Laravel\Prompts\multiselect;
use function Laravel\Prompts\suggest;

readonly class AnalysisInputCollector extends BaseInputCollector
{
    public function __construct(private AnalysisScriptsCollector $analysisScriptCollector) {}

    public function collect(): AnalysisOptions
    {
        $phpVersion = $this->detectPhpVersion();
        $framework = $this->detectFramework();
        $analysisTools = $this->selectAnalysisTools();
        $composerTools = $this->selectComposerTools();
        $install = $this->confirmInstall();

        return new AnalysisOptions(
            $phpVersion,
            $framework,
            $analysisTools,
            $composerTools,
            $install,
            $this->analysisScriptCollector->collect($analysisTools, $composerTools),
        );
    }

    private function detectPhpVersion(): PhpVersion
    {
        $version = suggest(
            label: Label::PHPVersion->value,
            options: PhpVersion::values(),
            default: PhpVersion::detect()->value,
            validate: $this->validatePhpVersion(...),
        );

        return PhpVersion::from($version);
    }

    /** @return array<AnalysisTool> */
    private function selectAnalysisTools(): array
    {
        $selected = multiselect(
            label: Label::AnalysisTools->value,
            options: AnalysisTool::values(),
            default: AnalysisTool::values(),
            hint: Message::MultiselectHint->value,
        );

        return array_map(
            static fn (int|string $value) => AnalysisTool::from((string) $value),
            $selected
        );
    }

    /** @return array<ComposerTool> */
    private function selectComposerTools(): array
    {
        $selected = multiselect(
            label: Label::ComposerTools->value,
            options: ComposerTool::values(),
            default: ComposerTool::values(),
            hint: Message::MultiselectHint->value,
        );

        return array_map(
            static fn (int|string $value) => ComposerTool::from((string) $value),
            $selected
        );
    }

    private function validatePhpVersion(string $value): ?string
    {
        return PhpVersion::tryFrom($value) !== null ? null : Message::InvalidPHPVersion->value;
    }
}
