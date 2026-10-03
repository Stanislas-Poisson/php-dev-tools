<?php

declare(strict_types=1);

namespace StanislasPoisson\DevTools;

/**
 * The commands that run the tools in a project: what `php-dev-tools cs`, `analyse`... execute.
 */
final readonly class Tools
{
    private const INSIGHTS = ['analyse', '--no-interaction', '--config-path=phpinsights.php'];

    public function __construct(private string $root) {}

    /**
     * The names of the commands that run tools, with a description.
     *
     * @return array<string, string>
     */
    public static function names(): array
    {
        return [
            'cs'           => 'Check the code style with Pint',
            'cs:fix'       => 'Fix the code style with Pint',
            'analyse'      => 'Run PHPStan at the maximum level with the strict rules',
            'rector'       => 'Check what Rector would change',
            'rector:fix'   => 'Apply the changes of Rector',
            'insights'     => 'Run PHP Insights, which must give 100 % everywhere',
            'markdown'     => 'Lint the Markdown files with markdownlint (needs Node.js)',
            'quality'      => 'Run the style, the analysis, Rector and PHP Insights',
            'quality:fast' => 'Run the style and the analysis (the pre-commit hook)',
            'quality:fix'  => 'Fix what can be fixed: Rector, then Pint',
        ];
    }

    /**
     * @return list<list<string>>|null the commands to run in turn, or null if the name is not one of a tool
     */
    public function commands(string $name): ?array
    {
        return match ($name) {
            'cs'           => [$this->pint(['--test'])],
            'cs:fix'       => [$this->pint([])],
            'analyse'      => [$this->bin('phpstan', ['analyse', '--memory-limit=1G', '--no-progress'])],
            'rector'       => [$this->bin('rector', ['process', '--dry-run', '--no-progress-bar'])],
            'rector:fix'   => [$this->bin('rector', ['process', '--no-progress-bar'])],
            'insights'     => [$this->bin('phpinsights', self::INSIGHTS)],
            'markdown'     => [['npx', '--yes', 'markdownlint-cli2', '**/*.md', '#vendor', '#build', '#node_modules']],
            'quality'      => $this->all(['cs', 'analyse', 'rector', 'insights']),
            'quality:fast' => $this->all(['cs', 'analyse']),
            'quality:fix'  => $this->all(['rector:fix', 'cs:fix']),
            default        => null,
        };
    }

    /**
     * @param list<string> $names
     *
     * @return list<list<string>>
     */
    private function all(array $names): array
    {
        $commands = [];

        foreach ($names as $name) {
            $commands = [...$commands, ...($this->commands($name) ?? [])];
        }

        return $commands;
    }

    /**
     * @param list<string> $arguments
     *
     * @return list<string>
     */
    private function bin(string $tool, array $arguments): array
    {
        return [$this->root . '/vendor/bin/' . $tool, ...$arguments];
    }

    /**
     * @param list<string> $arguments
     *
     * @return list<string>
     */
    private function pint(array $arguments): array
    {
        return $this->bin('pint', [
            ...$arguments,
            '--config=' . Pint::write($this->root),
            '--cache-file=' . $this->root . '/build/pint.cache',
        ]);
    }
}
