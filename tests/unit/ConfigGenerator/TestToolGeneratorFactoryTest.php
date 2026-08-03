<?php

declare(strict_types=1);

namespace Kgabryel\CodeQuality\Tests\Unit\ConfigGenerator;

use Kgabryel\CodeQuality\Installer\ConfigGenerator\TestToolGeneratorFactory;
use Kgabryel\CodeQuality\Installer\Dto\TestOptions;
use Kgabryel\CodeQuality\Installer\Enum\Project\Framework;
use Kgabryel\CodeQuality\Installer\Enum\Tool\TestTool;
use Kgabryel\CodeQuality\Installer\Filesystem;
use Kgabryel\CodeQuality\Tests\DataProvider\TestToolGeneratorDataProvider;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProviderExternal;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(TestToolGeneratorFactory::class)]
final class TestToolGeneratorFactoryTest extends TestCase
{
    #[Test]
    #[TestDox('Zwraca generator zgodny z narzędziem testowym')]
    #[DataProviderExternal(TestToolGeneratorDataProvider::class, 'tools')]
    public function itBuildsExpectedGenerator(TestTool $testTool, ?string $expectedClass): void
    {
        $factory = new TestToolGeneratorFactory(
            new Filesystem(),
            new TestOptions(
                Framework::None,
                TestTool::cases(),
                true,
                null
            )
        );

        $generator = $factory->build($testTool);

        if ($expectedClass === null) {
            self::assertNull($generator);

            return;
        }

        self::assertInstanceOf($expectedClass, $generator);
    }
}
