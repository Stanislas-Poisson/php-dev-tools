<?php

declare(strict_types=1);

namespace StanislasPoisson\DevTools\Tests;

use PHPUnit\Framework\TestCase;
use StanislasPoisson\DevTools\Rector;

final class RectorTest extends TestCase
{
    public function test_it_builds_the_configuration_of_rector_for_the_directories_that_exist(): void
    {
        $this->expectNotToPerformAssertions();

        $temporaryDirectory = new TemporaryDirectory();
        mkdir($temporaryDirectory->path . '/src');

        Rector::configure($temporaryDirectory->path, '8.3');
        Rector::configure($temporaryDirectory->path, '8.3', ['src', 'missing']);
    }
}
