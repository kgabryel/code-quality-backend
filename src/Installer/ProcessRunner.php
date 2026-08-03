<?php

declare(strict_types=1);

namespace Kgabryel\CodeQuality\Installer;

readonly class ProcessRunner
{
    public function run(string $command): ?int
    {
        $process = proc_open($command, [STDIN, STDOUT, STDERR], $pipes);

        if ($process === false) {
            return null;
        }

        return proc_close($process);
    }
}
