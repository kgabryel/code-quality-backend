<?php

declare(strict_types=1);

namespace Kgabryel\CodeQuality\Installer\Cli\Test;

use Kgabryel\CodeQuality\Installer\Cli\Input\BaseInputCollector;
use Kgabryel\CodeQuality\Installer\Dto\TestOptions;
use Kgabryel\CodeQuality\Installer\Enum\Text\Label;
use Kgabryel\CodeQuality\Installer\Enum\Text\Message;
use Kgabryel\CodeQuality\Installer\Enum\Tool\TestTool;

use function Laravel\Prompts\multiselect;

readonly class TestInputCollector extends BaseInputCollector
{
    public function __construct(private TestScriptsCollector $testScriptCollector) {}

    public function collect(): TestOptions
    {
        $framework = $this->detectFramework();
        $tools = $this->selectTools();
        $install = $this->confirmInstall();

        return new TestOptions(
            $framework,
            $tools,
            $install,
            $this->testScriptCollector->collect($tools),
        );
    }

    /** @return array<TestTool> */
    private function selectTools(): array
    {
        $selected = multiselect(
            label: Label::TestTools->value,
            options: TestTool::values(),
            default: TestTool::values(),
            hint: Message::MultiselectHint->value,
        );

        return array_map(
            static fn (int|string $value) => TestTool::from((string) $value),
            $selected
        );
    }
}
