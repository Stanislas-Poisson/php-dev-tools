<?php

declare(strict_types=1);

namespace StanislasPoisson\DevTools;

/**
 * Copies into a project the small files that extend the base ones of this package. A file that exists is never replaced.
 */
final readonly class Init
{
    private const array FILES = [
        'pint.json',
        'phpstan.neon.dist',
        'rector.php',
        'phpinsights.php',
        '.markdownlint.json',
        'Makefile',
    ];

    /**
     * @return list<string> the files that were created
     */
    public function copyTo(string $root): array
    {
        $created = [];

        foreach (self::FILES as $file) {
            if (! is_file($root . '/' . $file) && copy(__DIR__ . '/../stubs/' . $file, $root . '/' . $file)) {
                $created[] = $file;
            }
        }

        return $created;
    }
}
