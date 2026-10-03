<?php

declare(strict_types=1);

namespace StanislasPoisson\DevTools\Tests;

use PHPUnit\Framework\TestCase;
use StanislasPoisson\DevTools\Arrays;

final class ArraysTest extends TestCase
{
    public function test_map_is_empty_when_the_key_is_missing_or_is_not_an_array(): void
    {
        self::assertSame([], Arrays::map([], 'key'));
        self::assertSame([], Arrays::map(['key' => 'text'], 'key'));
    }

    public function test_map_keeps_the_string_keys_of_an_array(): void
    {
        self::assertSame(['a' => 1], Arrays::map(['key' => ['a' => 1, 0 => 2]], 'key'));
    }

    public function test_strings_is_empty_when_the_key_is_missing_or_is_not_an_array(): void
    {
        self::assertSame([], Arrays::strings([], 'key'));
        self::assertSame([], Arrays::strings(['key' => 1], 'key'));
    }

    public function test_strings_keeps_the_strings_of_an_array(): void
    {
        self::assertSame(['a', 'b'], Arrays::strings(['key' => ['a', 2, 'b', null]], 'key'));
    }
}
