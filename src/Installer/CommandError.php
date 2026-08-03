<?php

declare(strict_types=1);

namespace Kgabryel\CodeQuality\Installer;

use Kgabryel\CodeQuality\Installer\Enum\Text\Message;
use RuntimeException;

class CommandError extends RuntimeException
{
    private function __construct(string $message)
    {
        parent::__construct($message);
    }

    public static function aborted(): self
    {
        return new self(Message::Aborted->value);
    }

    public static function containerNameMissing(): self
    {
        return new self(Message::ContainerNameMissing->value);
    }

    public static function noPackages(): self
    {
        return new self(Message::NoPackages->value);
    }

    public static function packageRootUnavailable(): self
    {
        return new self(Message::PackageRootUnavailable->value);
    }

    public static function projectRootUnavailable(): self
    {
        return new self(Message::ProjectRootError->value);
    }

    public static function templateUnreadable(string $path): self
    {
        return new self(sprintf(Message::TemplateUnreadable->value, $path));
    }

    public static function writeFailed(string $path): self
    {
        return new self(sprintf(Message::WriteFailed->value, $path));
    }
}
