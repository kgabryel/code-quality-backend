<?php

declare(strict_types=1);

namespace Kgabryel\CodeQuality\Installer\ConfigGenerator\Tool;

use Kgabryel\CodeQuality\Installer\ConfigGenerator\AnalysisConfigGenerator;
use Kgabryel\CodeQuality\Installer\Enum\Project\Framework;
use Kgabryel\CodeQuality\Preset\CsFixer\BaseConfig;
use Kgabryel\CodeQuality\Preset\CsFixer\LaravelConfig;
use Kgabryel\CodeQuality\Preset\CsFixer\SymfonyConfig;

readonly class CsFixerGenerator extends AnalysisConfigGenerator
{
    public function getCacheName(): string
    {
        return '.php-cs-fixer.cache';
    }

    public function getFileName(): string
    {
        return '.php-cs-fixer.dist.php';
    }

    protected function getPlaceholders(): array
    {
        return [
            '{{CACHE_PATH}}' => $this->getCachePath(),
            '{{PRESET_CLASS}}' => $this->getConfigClassName(),
        ];
    }

    private function getConfigClassName(): string
    {
        return match ($this->installConfig->framework) {
            Framework::None => BaseConfig::class,
            Framework::Symfony => SymfonyConfig::class,
            Framework::Laravel => LaravelConfig::class,
        };
    }
}
