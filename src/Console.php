<?php

declare(strict_types=1);

namespace StanislasPoisson\DevTools;

use RuntimeException;

/**
 * The command `php-dev-tools`.
 */
final readonly class Console
{
    private const HOOKS = 'vendor/stanislas-poisson/php-dev-tools/hooks';

    public function __construct(
        private Runner $runner,
        private string $root,
    ) {}

    /**
     * @param list<string>          $arguments the arguments, without the name of the script
     * @param callable(string):void $out       writes on the standard output
     * @param callable(string):void $err       writes on the standard error
     *
     * @return int 0 on success, the code of the first command that fails, or 2 when the command is not valid
     */
    public function run(array $arguments, callable $out, callable $err): int
    {
        $name = $arguments[0] ?? 'help';

        if (in_array($name, ['help', '-h', '--help'], true)) {
            $out($this->help());

            return 0;
        }

        try {
            return match ($name) {
                'init'  => $this->init($out),
                'hooks' => $this->runner->run(['git', 'config', 'core.hooksPath', self::HOOKS], $this->root),
                default => $this->tool($name, $err),
            };
        }
        catch (RuntimeException $runtimeException) {
            $err($runtimeException->getMessage() . "\n");

            return 1;
        }
    }

    private function help(): string
    {
        $lines = ["Usage: php-dev-tools <command>\n", "\nCommands:\n"];

        $commands = [
            ...Tools::names(),
            'init'  => 'Copy the files that extend the base ones into the project',
            'hooks' => 'Activate the Git hooks of this package',
        ];

        foreach ($commands as $name => $description) {
            $lines[] = sprintf("  %-14s %s\n", $name, $description);
        }

        return implode('', $lines);
    }

    /**
     * @param callable(string):void $out
     */
    private function init(callable $out): int
    {
        $created = (new Init)->copyTo($this->root);

        $out([] === $created ? "Nothing to create: the files exist.\n" : 'Created: ' . implode(', ', $created) . "\n");

        return 0;
    }

    /**
     * @param callable(string):void $err
     */
    private function tool(string $name, callable $err): int
    {
        $commands = (new Tools($this->root))->commands($name);

        if (null === $commands) {
            $err(sprintf("Unknown command \"%s\".\n\n%s", $name, $this->help()));

            return 2;
        }

        foreach ($commands as $command) {
            $code = $this->runner->run($command, $this->root);

            if (0 !== $code) {
                return $code;
            }
        }

        return 0;
    }
}
