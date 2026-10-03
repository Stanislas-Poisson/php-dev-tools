<?php

declare(strict_types=1);

namespace StanislasPoisson\DevTools;

use Rector\Config\RectorConfig;
use Rector\Configuration\RectorConfigBuilder;

/**
 * The configuration of Rector of a project: the sets of laravel-dev-tools for the version of PHP of the project,
 * without the Laravel ones.
 */
final class Rector
{
    /**
     * @param string       $root  the root of the project
     * @param string       $php   the lowest version of PHP of the project, such as "8.3"
     * @param list<string> $paths the directories to process, relative to the root
     */
    public static function configure(
        string $root,
        string $php = '8.3',
        array $paths = ['src', 'tests'],
    ): RectorConfigBuilder {
        $directories = array_map(static fn (string $path): string => $root . '/' . $path, $paths);

        return RectorConfig::configure()
            ->withPaths(array_values(array_filter($directories, is_dir(...))))
            ->withCache($root . '/build/rector')
            ->withPhpSets(...['php' . str_replace('.', '', $php) => true])
            ->withPreparedSets(
                deadCode: true,
                codeQuality: true,
                codingStyle: true,
                typeDeclarations: true,
                privatization: true,
                naming: true,
                instanceOf: true,
                earlyReturn: true,
            )
            ->withParallel();
    }
}
