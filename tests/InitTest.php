<?php

declare(strict_types=1);

namespace StanislasPoisson\DevTools\Tests;

use PHPUnit\Framework\TestCase;
use StanislasPoisson\DevTools\Init;

final class InitTest extends TestCase
{
    public function test_it_copies_the_files_that_extend_the_base_ones(): void
    {
        $temporaryDirectory = new TemporaryDirectory;

        $created = (new Init)->copyTo($temporaryDirectory->path);

        self::assertSame(['pint.json', 'phpstan.neon.dist', 'rector.php', 'phpinsights.php', '.markdownlint.json', 'Makefile'], $created);

        foreach ($created as $file) {
            self::assertFileExists($temporaryDirectory->path . '/' . $file);
        }

        self::assertStringContainsString('vendor/stanislas-poisson/php-dev-tools/config/phpstan.neon', (string) file_get_contents($temporaryDirectory->path . '/phpstan.neon.dist'));
    }

    public function test_it_never_replaces_a_file(): void
    {
        $temporaryDirectory = new TemporaryDirectory;
        $temporaryDirectory->write('Makefile', 'mine');

        $created = (new Init)->copyTo($temporaryDirectory->path);

        self::assertNotContains('Makefile', $created);
        self::assertSame('mine', file_get_contents($temporaryDirectory->path . '/Makefile'));
        self::assertSame([], (new Init)->copyTo($temporaryDirectory->path));
    }
}
