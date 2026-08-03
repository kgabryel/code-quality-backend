<?php

declare(strict_types=1);

namespace Kgabryel\CodeQuality\Installer\Cli\Test;

use Kgabryel\CodeQuality\Installer\Cli\Input\BaseScriptsCollector;
use Kgabryel\CodeQuality\Installer\Dto\ScriptsOptions;
use Kgabryel\CodeQuality\Installer\Enum\Project\Environment;
use Kgabryel\CodeQuality\Installer\Enum\Script\TestScript;
use Kgabryel\CodeQuality\Installer\Enum\Text\Label;
use Kgabryel\CodeQuality\Installer\Enum\Text\Message;
use Kgabryel\CodeQuality\Installer\Enum\Tool\TestTool;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\multiselect;

readonly class TestScriptsCollector extends BaseScriptsCollector
{
    /**
     * @param array<TestTool> $tools
     *
     * @return null|ScriptsOptions<TestScript>
     */
    public function collect(array $tools): ?ScriptsOptions
    {
        $generateScripts = confirm(label: Label::GenerateScripts->value);

        if (!$generateScripts) {
            return null;
        }

        $environment = $this->detectEnvironment();

        return new ScriptsOptions(
            $environment,
            $this->selectScripts($tools),
            $environment === Environment::Docker ? $this->selectContainerName() : '',
        );
    }

    /**
     * @param array<TestTool> $tools
     *
     * @return array<TestScript>
     */
    private function selectScripts(array $tools): array
    {
        $selected = multiselect(
            label: Label::Scripts->value,
            options: TestScript::values($tools),
            default: TestScript::values($tools),
            hint: Message::MultiselectHint->value,
        );

        return array_map(
            static fn (int|string $value) => TestScript::from((string) $value),
            $selected
        );
    }
}
