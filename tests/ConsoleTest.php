<?php

declare(strict_types=1);

namespace StanislasPoisson\DevTools\Tests;

use PHPUnit\Framework\TestCase;
use StanislasPoisson\DevTools\Console;

final class ConsoleTest extends TestCase
{
    public function test_a_file_of_the_project_that_cannot_be_read_gives_an_error_and_the_code_1(): void
    {
        $temporaryDirectory = new TemporaryDirectory;
        $temporaryDirectory->write('pint.json', '{nope');

        [$code, $out, $err, $runner] = $this->execute(['cs'], [], $temporaryDirectory);

        self::assertSame(1, $code);
        self::assertSame('', $out);
        self::assertStringContainsString('is not valid JSON', $err);
        self::assertSame([], $runner->commands);
    }

    public function test_init_creates_the_files_and_tells_which(): void
    {
        [$code, $out, , $runner, $root] = $this->execute(['init']);

        self::assertSame(0, $code);
        self::assertStringStartsWith('Created: pint.json, phpstan.neon.dist', $out);
        self::assertFileExists($root->path . '/rector.php');
        self::assertSame([], $runner->commands);

        [, $again] = $this->execute(['init'], [], $root);

        self::assertSame("Nothing to create: the files exist.\n", $again);
    }

    public function test_it_activates_the_hooks(): void
    {
        [$code, , , $runner, $root] = $this->execute(['hooks']);

        self::assertSame(0, $code);
        self::assertSame([[['git', 'config', 'core.hooksPath', 'vendor/stanislas-poisson/php-dev-tools/hooks'], $root->path]], $runner->commands);
    }

    public function test_it_refuses_a_command_it_does_not_know(): void
    {
        [$code, $out, $err, $runner] = $this->execute(['deploy']);

        self::assertSame(2, $code);
        self::assertSame('', $out);
        self::assertStringStartsWith('Unknown command "deploy".', $err);
        self::assertStringContainsString('Usage: php-dev-tools', $err);
        self::assertSame([], $runner->commands);
    }

    public function test_it_runs_the_commands_of_a_tool_in_the_project(): void
    {
        [$code, , , $runner, $root] = $this->execute(['quality:fast']);

        self::assertSame(0, $code);
        self::assertCount(2, $runner->commands);
        self::assertSame($root->path, $runner->commands[0][1]);
        self::assertSame($root->path . '/vendor/bin/phpstan', $runner->commands[1][0][0]);
    }

    public function test_it_shows_the_help_without_a_command_or_when_asked(): void
    {
        foreach ([[], ['help'], ['-h'], ['--help']] as $arguments) {
            [$code, $out, , $runner] = $this->execute($arguments);

            self::assertSame(0, $code);
            self::assertStringStartsWith('Usage: php-dev-tools <command>', $out);
            self::assertStringContainsString('quality:fast', $out);
            self::assertStringContainsString('hooks', $out);
            self::assertSame([], $runner->commands);
        }
    }

    public function test_it_stops_at_the_first_command_that_fails_and_gives_its_code(): void
    {
        [$code, , , $runner] = $this->execute(['quality'], [0, 3]);

        self::assertSame(3, $code);
        self::assertCount(2, $runner->commands);
    }

    /**
     * @param list<string> $arguments
     * @param list<int>    $codes     the exit code of each command that is run
     *
     * @return array{int, string, string, RecordingRunner, TemporaryDirectory}
     */
    private function execute(array $arguments, array $codes = [], ?TemporaryDirectory $temporaryDirectory = null): array
    {
        $temporaryDirectory ??= new TemporaryDirectory;
        $recordingRunner = new RecordingRunner($codes);
        $out             = '';
        $err             = '';

        $code = (new Console($recordingRunner, $temporaryDirectory->path))->run(
            $arguments,
            static function (string $text) use (&$out): void {
                $out .= $text;
            },
            static function (string $text) use (&$err): void {
                $err .= $text;
            },
        );

        return [$code, $out, $err, $recordingRunner, $temporaryDirectory];
    }
}
