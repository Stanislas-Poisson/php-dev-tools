<?php

declare(strict_types=1);

namespace StanislasPoisson\DevTools\Tests;

use PHPUnit\Framework\TestCase;
use StanislasPoisson\DevTools\Tools;

final class ToolsTest extends TestCase
{
    public function test_a_name_that_is_not_a_tool_has_no_commands(): void
    {
        self::assertNull((new Tools('/tmp'))->commands('deploy'));
    }

    public function test_every_name_has_commands(): void
    {
        $temporaryDirectory = new TemporaryDirectory;
        $tools              = new Tools($temporaryDirectory->path);

        foreach (array_keys(Tools::names()) as $name) {
            self::assertNotEmpty($tools->commands($name), $name);
        }
    }

    public function test_pint_runs_with_the_merged_configuration_and_a_cache_in_the_build_directory(): void
    {
        $temporaryDirectory = new TemporaryDirectory;
        $bin                = $temporaryDirectory->path . '/vendor/bin/pint';

        $check = (new Tools($temporaryDirectory->path))->commands('cs');
        $fix   = (new Tools($temporaryDirectory->path))->commands('cs:fix');

        $arguments = ['--config=' . $temporaryDirectory->path . '/build/pint.json', '--cache-file=' . $temporaryDirectory->path . '/build/pint.cache'];

        self::assertSame([[$bin, '--test', ...$arguments]], $check);
        self::assertSame([[$bin, ...$arguments]], $fix);
    }

    public function test_the_gates_run_the_tools_in_turn(): void
    {
        $temporaryDirectory = new TemporaryDirectory;
        $tools              = new Tools($temporaryDirectory->path);

        self::assertSame(['pint', 'phpstan', 'rector', 'phpinsights'], $this->programs($tools->commands('quality')));
        self::assertSame(['pint', 'phpstan'], $this->programs($tools->commands('quality:fast')));
        self::assertSame(['rector', 'pint'], $this->programs($tools->commands('quality:fix')));
    }

    public function test_the_other_tools_run_from_the_vendor_directory_of_the_project(): void
    {
        $tools = new Tools('/project');

        self::assertSame([['/project/vendor/bin/phpstan', 'analyse', '--memory-limit=1G', '--no-progress']], $tools->commands('analyse'));
        self::assertSame([['/project/vendor/bin/rector', 'process', '--dry-run', '--no-progress-bar']], $tools->commands('rector'));
        self::assertSame([['/project/vendor/bin/rector', 'process', '--no-progress-bar']], $tools->commands('rector:fix'));
        self::assertSame(
            [['/project/vendor/bin/phpinsights', 'analyse', '--no-interaction', '--config-path=phpinsights.php']],
            $tools->commands('insights'),
        );
        self::assertSame(
            [['npx', '--yes', 'markdownlint-cli2', '**/*.md', '#vendor', '#build', '#node_modules']],
            $tools->commands('markdown'),
        );
    }

    /**
     * @param list<list<string>>|null $commands
     *
     * @return list<string> the name of the program of each command
     */
    private function programs(?array $commands): array
    {
        $programs = [];

        foreach ($commands ?? [] as $command) {
            $programs[] = basename($command[0]);
        }

        return $programs;
    }
}
