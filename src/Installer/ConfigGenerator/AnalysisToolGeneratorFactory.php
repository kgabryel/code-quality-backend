<?php

declare(strict_types=1);

namespace Kgabryel\CodeQuality\Installer\ConfigGenerator;

use Kgabryel\CodeQuality\Installer\ConfigGenerator\Tool\CsFixerGenerator;
use Kgabryel\CodeQuality\Installer\ConfigGenerator\Tool\PhpCsGenerator;
use Kgabryel\CodeQuality\Installer\ConfigGenerator\Tool\PhpStanGenerator;
use Kgabryel\CodeQuality\Installer\ConfigGenerator\Tool\RectorGenerator;
use Kgabryel\CodeQuality\Installer\Dto\AnalysisOptions;
use Kgabryel\CodeQuality\Installer\Enum\Tool\AnalysisTool;
use Kgabryel\CodeQuality\Installer\Filesystem;

readonly class AnalysisToolGeneratorFactory
{
    public function __construct(private Filesystem $filesystem, private AnalysisOptions $installConfig) {}

    public function build(AnalysisTool $tool): ?ConfigGenerator
    {
        return match ($tool) {
            AnalysisTool::Rector => new RectorGenerator($this->filesystem, $this->installConfig),
            AnalysisTool::CsFixer => new CsFixerGenerator($this->filesystem, $this->installConfig),
            AnalysisTool::PhpStan => new PhpStanGenerator($this->filesystem, $this->installConfig),
            AnalysisTool::Phpcs => new PhpCsGenerator($this->filesystem, $this->installConfig),
            AnalysisTool::ParallelLint => null,
        };
    }
}
