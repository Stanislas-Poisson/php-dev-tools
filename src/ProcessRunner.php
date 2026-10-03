<?php

declare(strict_types=1);

namespace StanislasPoisson\DevTools;

/**
 * Runs a command in a process that writes on the terminal of the caller.
 */
final class ProcessRunner implements Runner
{
    /**
     * @param list<string> $command
     */
    public function run(array $command, string $directory): int
    {
        $process = proc_open($command, [0 => STDIN, 1 => STDOUT, 2 => STDERR], $pipes, $directory);

        return is_resource($process) ? proc_close($process) : 1;
    }
}
