<?php

declare(strict_types=1);

namespace Kgabryel\CodeQuality\Installer\Dto;

use Kgabryel\CodeQuality\Installer\CommandError;
use Kgabryel\CodeQuality\Installer\Enum\Project\Environment;
use Kgabryel\CodeQuality\Installer\Enum\Script\Script;

/** @template T of Script */
readonly class ScriptsOptions
{
    /**
     * @param array<T> $scripts
     */
    public function __construct(
        public Environment $environment,
        public array $scripts,
        public string $containerName = '',
    ) {
        if ($environment === Environment::Docker && $containerName === '') {
            throw CommandError::containerNameMissing();
        }
    }
}
