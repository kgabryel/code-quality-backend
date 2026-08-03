<?php

declare(strict_types=1);

namespace Kgabryel\CodeQuality\Tests\Unit\Dto;

use Kgabryel\CodeQuality\Installer\CommandError;
use Kgabryel\CodeQuality\Installer\Dto\ScriptsOptions;
use Kgabryel\CodeQuality\Installer\Enum\Project\Environment;
use Kgabryel\CodeQuality\Installer\Enum\Script\TestScript;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DoesNotPerformAssertions;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(ScriptsOptions::class)]
final class ScriptsOptionsTest extends TestCase
{
    #[Test]
    #[TestDox('Nie rzuca wyjątku, gdy środowisko to Docker, a nazwa kontenera jest podana')]
    #[DoesNotPerformAssertions]
    public function itAllowsDockerWithName(): void
    {
        // Act
        new ScriptsOptions(Environment::Docker, [TestScript::PhpUnit], 'my-container');
    }

    #[Test]
    #[TestDox('Nie rzuca wyjątku, gdy środowisko to Local, a nazwa kontenera jest pusta')]
    #[DoesNotPerformAssertions]
    public function itAllowsLocalWithoutName(): void
    {
        // Act
        new ScriptsOptions(Environment::Local, [TestScript::PhpUnit], '');
    }

    #[Test]
    #[TestDox('Rzuca wyjątek, gdy środowisko to Docker, a nazwa kontenera jest pusta')]
    public function itThrowsWhenNameMissing(): void
    {
        // Assert
        $this->expectException(CommandError::class);

        // Act
        new ScriptsOptions(Environment::Docker, [TestScript::PhpUnit], '');
    }
}
