<?php

declare(strict_types=1);

namespace Kgabryel\CodeQuality\Installer;

readonly class Filesystem
{
    public function fileExists(string $path): bool
    {
        return file_exists($path);
    }

    public function getCurrentDirectory(): false|string
    {
        return getcwd();
    }

    public function makeExecutable(string $path): bool
    {
        return @chmod($path, 0755);
    }

    public function readFile(string $path): false|string
    {
        return file_get_contents($path);
    }

    public function writeFile(string $path, string $content): false|int
    {
        return file_put_contents($path, $content);
    }
}
