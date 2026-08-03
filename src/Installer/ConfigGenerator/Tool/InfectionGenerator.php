<?php

declare(strict_types=1);

namespace Kgabryel\CodeQuality\Installer\ConfigGenerator\Tool;

use JsonException;
use Kgabryel\CodeQuality\Installer\ConfigGenerator\TestConfigGenerator;
use Kgabryel\CodeQuality\Installer\Enum\Project\Framework;

readonly class InfectionGenerator extends TestConfigGenerator
{
    private const int EXCLUDES_INDENT = 6;

    private const int IGNORED_CALLS_INDENT = 8;

    private const int PATHS_INDENT = 6;

    public function getCacheName(): string
    {
        return '.infection/';
    }

    public function getFileName(): string
    {
        return 'infection.json5';
    }

    protected function getPlaceholders(): array
    {
        return [
            '{{CACHE_PATH}}' => $this->getCachePath(),
            '{{EXCLUDES}}' => $this->renderList($this->getExcludes(), self::EXCLUDES_INDENT),
            '{{IGNORED_CALLS}}' => $this->renderList($this->getIgnoredCalls(), self::IGNORED_CALLS_INDENT),
            '{{PATHS}}' => $this->renderList($this->getPaths(), self::PATHS_INDENT),
        ];
    }

    /** @return list<string> */
    private function getExcludes(): array
    {
        return match ($this->installConfig->framework) {
            Framework::Symfony => ['Kernel.php'],
            Framework::Laravel, Framework::None => [],
        };
    }

    /** @return list<string> */
    private function getIgnoredCalls(): array
    {
        return match ($this->installConfig->framework) {
            Framework::Symfony => ['App\Entity\*::*', '*::__construct'],
            Framework::Laravel => ['App\Models\*::*', '*::__construct'],
            Framework::None => ['*::__construct'],
        };
    }

    /** @return list<string> */
    private function getPaths(): array
    {
        return match ($this->installConfig->framework) {
            Framework::Laravel => ['app'],
            Framework::Symfony, Framework::None => ['src'],
        };
    }

    /** @param list<string> $items
     * @throws JsonException
     */
    private function renderList(array $items, int $indent): string
    {
        return implode(",\n", array_map(
            static fn (string $item): string => sprintf(
                '%s%s',
                str_repeat(' ', $indent),
                json_encode($item, JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR)
            ),
            $items,
        ));
    }
}
