<?php

declare(strict_types=1);

namespace StanislasPoisson\DevTools\Tests;

use PHPUnit\Framework\TestCase;
use StanislasPoisson\DevTools\ProcessRunner;

final class ProcessRunnerTest extends TestCase
{
    public function test_it_gives_1_when_the_command_cannot_start(): void
    {
        set_error_handler(static fn (): bool => true);

        try {
            self::assertSame(1, (new ProcessRunner)->run([PHP_BINARY, '-v'], '/this/directory/does/not/exist'));
        }
        finally {
            restore_error_handler();
        }
    }

    public function test_it_gives_the_exit_code_of_the_command(): void
    {
        $processRunner = new ProcessRunner;

        self::assertSame(0, $processRunner->run([PHP_BINARY, '-r', 'exit(0);'], sys_get_temp_dir()));
        self::assertSame(3, $processRunner->run([PHP_BINARY, '-r', 'exit(3);'], sys_get_temp_dir()));
    }

    public function test_it_runs_the_command_in_the_directory(): void
    {
        $temporaryDirectory = new TemporaryDirectory;

        self::assertSame(0, (new ProcessRunner)->run([PHP_BINARY, '-r', 'exit(getcwd() === $argv[1] ? 0 : 1);', $temporaryDirectory->path], $temporaryDirectory->path));
    }
}
