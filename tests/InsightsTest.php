<?php

declare(strict_types=1);

namespace StanislasPoisson\DevTools\Tests;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use StanislasPoisson\DevTools\Insights;

final class InsightsTest extends TestCase
{
    #[Test]
    public function it_any_other_key_replaces_the_base(): void
    {
        self::assertSame('laravel', Insights::config(['preset' => 'laravel'])['preset']);
    }

    #[Test]
    public function it_the_base_asks_for_100_percent(): void
    {
        $requirements = Insights::config()['requirements'];

        self::assertIsArray($requirements);
        self::assertSame(100, $requirements['min-quality']);
        self::assertSame(100, $requirements['min-complexity']);
        self::assertSame(100, $requirements['min-architecture']);
        self::assertSame(100, $requirements['min-style']);
    }

    #[Test]
    public function it_the_config_and_the_requirements_of_the_project_replace_the_ones_of_the_base_by_key(): void
    {
        $config = Insights::config([
            'config'       => ['AnInsight' => ['exclude' => ['src/Models']]],
            'requirements' => ['min-style' => 90],
        ]);

        self::assertIsArray($config['config']);
        self::assertIsArray($config['requirements']);
        self::assertSame(['exclude' => ['src/Models']], $config['config']['AnInsight']);
        self::assertSame(90, $config['requirements']['min-style']);
        self::assertSame(100, $config['requirements']['min-quality']);
    }

    #[Test]
    public function it_the_exclusions_and_the_removed_rules_are_added_to_the_base(): void
    {
        $base   = Insights::config();
        $config = Insights::config(['exclude' => ['generated.php'], 'remove' => ['AnInsight']]);

        self::assertIsArray($base['exclude']);
        self::assertIsArray($base['remove']);
        self::assertSame([...$base['exclude'], 'generated.php'], $config['exclude']);
        self::assertSame([...$base['remove'], 'AnInsight'], $config['remove']);
    }
}
