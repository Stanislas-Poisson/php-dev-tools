<?php

declare(strict_types=1);

namespace StanislasPoisson\DevTools;

/**
 * Runs a command of a tool.
 */
interface Runner
{
    /**
     * @param list<string> $command   the program and its arguments
     * @param string       $directory the directory in which to run it
     *
     * @return int the exit code
     */
    public function run(array $command, string $directory): int;
}
