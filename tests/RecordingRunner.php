<?php

declare(strict_types=1);

namespace StanislasPoisson\DevTools\Tests;

use StanislasPoisson\DevTools\Runner;

/**
 * A runner that runs nothing: it records the commands, and answers with the codes it was given.
 */
final class RecordingRunner implements Runner
{
    /**
     * @var list<array{list<string>, string}>
     */
    public array $commands = [];

    /**
     * @param list<int> $codes the exit code of each command, in turn; 0 once they are used
     */
    public function __construct(private array $codes = []) {}

    public function run(array $command, string $directory): int
    {
        $this->commands[] = [$command, $directory];

        return array_shift($this->codes) ?? 0;
    }
}
