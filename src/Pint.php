<?php

declare(strict_types=1);

namespace StanislasPoisson\DevTools;

use JsonException;
use RuntimeException;

/**
 * The configuration of Pint of a project: the base rules of this package, and the `pint.json` of the project, which
 * adds its own rules and its own exclusions.
 */
final class Pint
{
    /**
     * The rules of the project replace the ones of the base, and its exclusions are added to the ones of the base.
     *
     * @param array<string, mixed> $base
     * @param array<string, mixed> $project
     *
     * @return array<string, mixed>
     */
    public static function merge(array $base, array $project): array
    {
        $baseFinder    = Arrays::map($base, 'finder');
        $projectFinder = Arrays::map($project, 'finder');

        return [
            ...$base,
            ...$project,
            'rules'  => [...Arrays::map($base, 'rules'), ...Arrays::map($project, 'rules')],
            'finder' => [
                ...$baseFinder,
                ...$projectFinder,
                'exclude' => array_values(array_unique([
                    'build',
                    'vendor',
                    ...Arrays::strings($baseFinder, 'exclude'),
                    ...Arrays::strings($projectFinder, 'exclude'),
                ])),
            ],
        ];
    }

    /**
     * Writes the configuration that Pint has to use in the `build` directory of the project.
     *
     * @return string the path of the file
     *
     * @throws RuntimeException when a file cannot be read or written
     */
    public static function write(string $root): string
    {
        $directory = $root . '/build';
        self::createDirectory($directory);
        self::dropCache($directory);

        $json = json_encode(
            self::merge(self::read(__DIR__ . '/../config/pint.json'), self::read($root . '/pint.json')),
            JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES,
        );

        if (false === file_put_contents($directory . '/pint.json', $json . "\n")) {
            throw new RuntimeException(sprintf('The file "%s" cannot be written.', $directory . '/pint.json'));
        }

        return $directory . '/pint.json';
    }

    private static function createDirectory(string $directory): void
    {
        if (! is_dir($directory) && ! mkdir($directory, 0o775, true) && ! is_dir($directory)) {
            throw new RuntimeException(sprintf('The directory "%s" cannot be created.', $directory));
        }
    }

    /**
     * A check never relies on the result of an old run: the cache of Pint is dropped.
     */
    private static function dropCache(string $directory): void
    {
        if (is_file($directory . '/pint.cache')) {
            unlink($directory . '/pint.cache');
        }
    }

    /**
     * @return array<string, mixed> the content of a JSON file, none if the file does not exist
     */
    private static function read(string $path): array
    {
        if (! is_file($path)) {
            return [];
        }

        try {
            $content = json_decode((string) file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);
        }
        catch (JsonException $jsonException) {
            throw new RuntimeException(
                sprintf('The file "%s" is not valid JSON: %s', $path, $jsonException->getMessage()),
                0,
                $jsonException,
            );
        }

        return is_array($content) ? array_filter($content, is_string(...), ARRAY_FILTER_USE_KEY) : [];
    }
}
