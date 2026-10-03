<?php

declare(strict_types=1);

namespace StanislasPoisson\DevTools;

/**
 * Reads the parts of a configuration that is an array of unknown content.
 */
final class Arrays
{
    /**
     * @param array<mixed> $config
     *
     * @return array<string, mixed> the array at the key, none if it is not an array
     */
    public static function map(array $config, string $key): array
    {
        $value = $config[$key] ?? [];

        return is_array($value) ? array_filter($value, is_string(...), ARRAY_FILTER_USE_KEY) : [];
    }

    /**
     * @param array<mixed> $config
     *
     * @return list<string> the strings of the array at the key
     */
    public static function strings(array $config, string $key): array
    {
        $value = $config[$key] ?? [];

        return is_array($value) ? array_values(array_filter($value, is_string(...))) : [];
    }
}
