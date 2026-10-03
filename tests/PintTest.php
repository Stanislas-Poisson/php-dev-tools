<?php

declare(strict_types=1);

namespace StanislasPoisson\DevTools\Tests;

use PHPUnit\Framework\TestCase;
use RuntimeException;
use StanislasPoisson\DevTools\Pint;

final class PintTest extends TestCase
{
    public function test_the_exclusions_of_the_project_are_added_and_build_and_vendor_are_always_excluded(): void
    {
        $merged = Pint::merge(['finder' => ['exclude' => ['x']]], ['finder' => ['exclude' => ['data', 'x'], 'name' => '*.php']]);

        self::assertSame(['exclude' => ['build', 'vendor', 'x', 'data'], 'name' => '*.php'], $merged['finder']);
        self::assertSame(['exclude' => ['build', 'vendor']], Pint::merge([], [])['finder']);
    }

    public function test_the_rules_of_the_project_replace_the_ones_of_the_base(): void
    {
        $merged = Pint::merge(
            ['preset' => 'laravel', 'rules' => ['a' => true, 'b' => true]],
            ['rules' => ['b' => false, 'c' => true]],
        );

        self::assertSame('laravel', $merged['preset']);
        self::assertSame(['a' => true, 'b' => false, 'c' => true], $merged['rules']);
    }

    public function test_write_ignores_a_file_of_the_project_that_is_not_an_object(): void
    {
        $temporaryDirectory = new TemporaryDirectory;
        $temporaryDirectory->write('pint.json', '3');

        self::assertFileExists(Pint::write($temporaryDirectory->path));
    }

    public function test_write_merges_the_base_and_the_file_of_the_project_in_the_build_directory(): void
    {
        $temporaryDirectory = new TemporaryDirectory;
        $temporaryDirectory->write('pint.json', '{"rules": {"yoda_style": false}, "finder": {"exclude": ["data"]}}');

        $path   = Pint::write($temporaryDirectory->path);
        $config = json_decode((string) file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);

        self::assertSame($temporaryDirectory->path . '/build/pint.json', $path);
        self::assertIsArray($config);
        self::assertSame('laravel', $config['preset']);
        self::assertIsArray($config['rules']);
        self::assertFalse($config['rules']['yoda_style']);
        self::assertTrue($config['rules']['strict_comparison']);
        self::assertIsArray($config['finder']);
        self::assertSame(['build', 'vendor', 'data'], $config['finder']['exclude']);
    }

    public function test_write_refuses_a_build_directory_that_cannot_be_created(): void
    {
        $temporaryDirectory = new TemporaryDirectory;
        $temporaryDirectory->write('build', 'a file');

        set_error_handler(static fn (): bool => true);

        try {
            $this->expectException(RuntimeException::class);
            $this->expectExceptionMessage('cannot be created');

            Pint::write($temporaryDirectory->path);
        }
        finally {
            restore_error_handler();
        }
    }

    public function test_write_refuses_a_file_of_the_project_that_is_not_json(): void
    {
        $temporaryDirectory = new TemporaryDirectory;
        $temporaryDirectory->write('pint.json', '{nope');

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('is not valid JSON');

        Pint::write($temporaryDirectory->path);
    }

    public function test_write_refuses_a_file_that_cannot_be_written(): void
    {
        $temporaryDirectory = new TemporaryDirectory;
        mkdir($temporaryDirectory->path . '/build/pint.json', 0o775, true);

        set_error_handler(static fn (): bool => true);

        try {
            $this->expectException(RuntimeException::class);
            $this->expectExceptionMessage('cannot be written');

            Pint::write($temporaryDirectory->path);
        }
        finally {
            restore_error_handler();
        }
    }

    public function test_write_works_without_a_file_in_the_project_and_drops_the_cache_of_pint(): void
    {
        $temporaryDirectory = new TemporaryDirectory;
        mkdir($temporaryDirectory->path . '/build');
        $temporaryDirectory->write('build/pint.cache', 'old');

        Pint::write($temporaryDirectory->path);

        self::assertFileDoesNotExist($temporaryDirectory->path . '/build/pint.cache');
        self::assertFileExists($temporaryDirectory->path . '/build/pint.json');
    }
}
