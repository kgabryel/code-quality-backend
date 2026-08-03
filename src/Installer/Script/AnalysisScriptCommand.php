<?php

declare(strict_types=1);

namespace Kgabryel\CodeQuality\Installer\Script;

use Kgabryel\CodeQuality\Installer\Dto\AnalysisOptions;
use Kgabryel\CodeQuality\Installer\Enum\Project\Framework;
use Kgabryel\CodeQuality\Installer\Enum\Script\AnalysisScript;
use Kgabryel\CodeQuality\Installer\Enum\Script\Script;

/** @implements ScriptCommand<AnalysisScript> */
readonly class AnalysisScriptCommand implements ScriptCommand
{
    private const string PHP_STAN_MEMORY_LIMIT = '512M';

    public function __construct(private AnalysisOptions $installConfig) {}

    public function get(Script $script): string
    {
        return match ($script) {
            AnalysisScript::CsFixer => 'php vendor/bin/php-cs-fixer fix',
            AnalysisScript::PhpcsCheck => 'php vendor/bin/phpcs',
            AnalysisScript::PhpcsFix => 'php vendor/bin/phpcbf',
            AnalysisScript::PhpStan => sprintf(
                'php vendor/bin/phpstan analyse --memory-limit=%s',
                self::PHP_STAN_MEMORY_LIMIT
            ),
            AnalysisScript::Rector => 'php vendor/bin/rector process',
            AnalysisScript::ParallelLint => $this->getParallelLint(),
            AnalysisScript::ComposerRequireChecker => 'php vendor/bin/composer-require-checker',
            AnalysisScript::ComposerUnused => 'php vendor/bin/composer-unused',
            AnalysisScript::ComposerNormalize => 'composer normalize',
            AnalysisScript::ComposerCheck => $this->getComposerCheck(),
            AnalysisScript::CodeFix => $this->getCodeFix(),
            AnalysisScript::CodeCheck => $this->getCodeCheck(),
            AnalysisScript::ComposerDeps => $this->getComposerDeps(),
        };
    }

    /**
     * @param list<AnalysisScript> $scripts
     */
    private function buildScript(array $scripts): string
    {
        $applicable = array_filter(
            $scripts,
            fn (AnalysisScript $script): bool => $script->isApplicable(
                $this->installConfig->analysisTools,
                $this->installConfig->composerTools
            )
        );

        return implode("\n\n", array_map(
            fn (AnalysisScript $script): string => $this->wrapCommand($script->value, $this->get($script)),
            $applicable
        ));
    }

    private function getCodeCheck(): string
    {
        return $this->buildScript([
            AnalysisScript::ParallelLint,
            AnalysisScript::PhpcsCheck,
            AnalysisScript::PhpStan,
        ]);
    }

    private function getCodeFix(): string
    {
        return $this->buildScript([
            AnalysisScript::CsFixer,
            AnalysisScript::PhpcsFix,
            AnalysisScript::Rector,
        ]);
    }

    private function getComposerCheck(): string
    {
        return implode("\n\n", [
            $this->wrapCommand('Composer Validate', 'composer validate --strict --no-check-publish'),
            $this->wrapCommand('Composer Audit', 'composer audit'),
            $this->wrapCommand('Composer Outdated', 'composer outdated'),
        ]);
    }

    private function getComposerDeps(): string
    {
        return $this->buildScript([
            AnalysisScript::ComposerRequireChecker,
            AnalysisScript::ComposerUnused,
            AnalysisScript::ComposerNormalize,
        ]);
    }

    /** @return list<string> */
    private function getLintDirectories(): array
    {
        return match ($this->installConfig->framework) {
            Framework::Symfony => ['bin', 'config', 'public', 'src', 'tests'],
            Framework::Laravel => ['app', 'bootstrap', 'config', 'database', 'routes', 'tests'],
            Framework::None => ['src', 'tests'],
        };
    }

    /** @return list<string> */
    private function getLintExcludes(): array
    {
        return match ($this->installConfig->framework) {
            Framework::Laravel => ['bootstrap/cache'],
            Framework::Symfony, Framework::None => [],
        };
    }

    private function getParallelLint(): string
    {
        $dirs = implode(' ', $this->getLintDirectories());
        $excludes = implode(
            ' ',
            array_map(
                static fn (string $ex): string => '--exclude ' . $ex,
                $this->getLintExcludes()
            )
        );

        return trim(sprintf('php vendor/bin/parallel-lint %s %s', $dirs, $excludes));
    }

    private function wrapCommand(string $label, string $command): string
    {
        return sprintf(
            "echo \"===== %s =====\"\n%s && echo \"✓ %s passed\" || echo \"✗ %s failed\"",
            $label,
            $command,
            $label,
            $label,
        );
    }
}
