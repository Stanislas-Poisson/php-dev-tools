<?php

declare(strict_types=1);

namespace StanislasPoisson\DevTools\Tests;

use StanislasPoisson\DevTools\Runner;

/**
 * Runs a command and keeps what it writes out of the output of the tests.
 */
final class ProcessRunnerQuiet implements Runner
{
    public function run(array $command, string $directory): int
    {
        $process = proc_open($command, [1 => ['pipe', 'w'], 2 => ['pipe', 'w']], $pipes, $directory);

        if (! is_resource($process)) {
            return 1;
        }

        stream_get_contents($pipes[1]);
        stream_get_contents($pipes[2]);

        return proc_close($process);
    }
}
