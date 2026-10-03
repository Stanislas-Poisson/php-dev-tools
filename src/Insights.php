<?php

declare(strict_types=1);

namespace StanislasPoisson\DevTools;

/**
 * The configuration of PHP Insights of a project: the base configuration of this package, and what the project changes.
 */
final class Insights
{
    /**
     * @param array<string, mixed> $overrides what the project changes. The lists `remove` and `exclude` are added to
     *                                        the ones of the base, the keys of `add`, `config` and `requirements`
     *                                        replace the ones of the base, and any other key replaces the base
     *
     * @return array<string, mixed>
     */
    public static function config(array $overrides = []): array
    {
        /** @var array<string, mixed> $base */
        $base = require __DIR__ . '/../config/phpinsights.php';

        foreach (['remove', 'exclude'] as $list) {
            $overrides[$list] = array_values(array_unique([
                ...Arrays::strings($base, $list),
                ...Arrays::strings($overrides, $list),
            ]));
        }

        foreach (['add', 'config', 'requirements'] as $map) {
            $overrides[$map] = [...Arrays::map($base, $map), ...Arrays::map($overrides, $map)];
        }

        return [...$base, ...$overrides];
    }
}
