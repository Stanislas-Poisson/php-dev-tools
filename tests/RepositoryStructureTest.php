<?php

declare(strict_types=1);

namespace StanislasPoisson\DevTools\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * The files and the contents that must stay, or be corrected with the test that names them. They run against the
 * source tree, not against an installed package. The other zairakai PHP repositories keep a copy of this test, with
 * the checks of their own files.
 */
final class RepositoryStructureTest extends TestCase
{
    /**
     * @return iterable<string, array{string}>
     */
    public static function communityFiles(): iterable
    {
        foreach ([
            'LICENSE', 'README.md', 'SECURITY.md', 'CONTRIBUTING.md', 'CODE_OF_CONDUCT.md', 'composer.json',
            '.editorconfig', '.gitattributes', '.gitignore', 'phpunit.xml',
        ] as $file) {
            yield $file => [$file];
        }
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function configFiles(): iterable
    {
        foreach ([
            'pint.json', 'phpstan.neon', 'phpinsights.php', '.markdownlint.json', '.markdownlintignore',
        ] as $file) {
            yield $file => [$file];
        }
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function copiedFiles(): iterable
    {
        foreach (['pint.json', '.markdownlintignore'] as $file) {
            yield $file => [$file];
        }
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function thinFiles(): iterable
    {
        foreach ([
            'pint.json', 'phpstan.neon', 'rector.php', 'phpinsights.php', '.markdownlint.json', '.markdownlintignore',
        ] as $file) {
            yield $file => [$file];
        }
    }

    #[Test]
    public function it_asks_for_a_subject_of_50_characters_at_most_in_the_pull_request_template(): void
    {
        $template = (string) file_get_contents($this->root('.github/pull_request_template.md'));

        self::assertStringContainsString('50 characters at most', $template);
    }

    #[Test]
    public function it_folds_the_long_sections_of_the_readme_in_details(): void
    {
        $readme = (string) file_get_contents($this->root('README.md'));

        self::assertStringContainsString('<details>', $readme);
        self::assertStringContainsString('<summary>', $readme);
    }

    #[Test]
    public function it_has_no_command_no_hook_and_no_changelog(): void
    {
        foreach (['bin', 'hooks', 'Makefile', 'Makefile.inc', 'CHANGELOG.md'] as $path) {
            self::assertFileDoesNotExist($this->root($path));
        }
    }

    // Writing rules
    // ================

    #[Test]
    public function it_has_no_long_dash_in_the_files_of_the_repository(): void
    {
        // The long dashes are written with their bytes: this file does not hold them.
        $em = "\xe2\x80\x94";
        $en = "\xe2\x80\x93";

        $git   = 'git ls-files --cached --others --exclude-standard 2>/dev/null';
        $files = [];

        foreach (explode("\n", (string) shell_exec('cd ' . escapeshellarg($this->root()) . ' && ' . $git)) as $file) {
            if ('' !== $file && is_file($this->root($file)) && 'composer.lock' !== $file) {
                $files[] = $file;
            }
        }

        if ([] === $files) {
            self::markTestSkipped('The list of the files needs git.');
        }

        $found = [];

        foreach ($files as $file) {
            $content = (string) file_get_contents($this->root($file));

            if (! str_contains($content, "\0") && (str_contains($content, $em) || str_contains($content, $en))) {
                $found[] = $file;
            }
        }

        self::assertSame([], $found);
    }

    // Root
    // ================

    #[Test]
    #[DataProvider('communityFiles')]
    public function it_has_the_files_of_the_repository_at_the_root(string $file): void
    {
        self::assertFileExists($this->root($file));
    }

    #[Test]
    public function it_has_the_scripts_of_the_other_repositories(): void
    {
        $json     = (string) file_get_contents($this->root('composer.json'));
        $composer = json_decode($json, true, 512, JSON_THROW_ON_ERROR);

        self::assertIsArray($composer);
        self::assertIsArray($composer['scripts']);

        foreach ([
            'cs', 'cs:fix', 'cs-fix', 'analyse', 'rector', 'rector:fix', 'rector-fix', 'insights', 'markdown',
            'quality', 'quality:fix', 'quality-fix', 'test',
        ] as $name) {
            self::assertArrayHasKey($name, $composer['scripts']);
        }

        self::assertArrayNotHasKey('bin', $composer);
    }

    // config/ and stubs/
    // ================

    #[Test]
    #[DataProvider('configFiles')]
    public function it_holds_the_base_of_each_tool_in_config(string $file): void
    {
        self::assertFileExists($this->root('config/' . $file));
    }

    #[Test]
    public function it_holds_the_icon_and_the_banner_in_both_formats(): void
    {
        foreach (['icon.png', 'icon.svg', 'banner.png', 'banner.svg'] as $file) {
            self::assertFileExists($this->root('assets/' . $file));
        }
    }

    #[Test]
    #[DataProvider('thinFiles')]
    public function it_holds_the_thin_files_at_the_root_and_in_stubs(string $file): void
    {
        self::assertFileExists($this->root($file));
        self::assertFileExists($this->root('stubs/' . $file));
    }

    #[Test]
    #[DataProvider('copiedFiles')]
    public function it_keeps_the_copies_of_the_base_identical(string $file): void
    {
        $reference = (string) file_get_contents($this->root('config/' . $file));

        self::assertSame($reference, (string) file_get_contents($this->root($file)));
        self::assertSame($reference, (string) file_get_contents($this->root('stubs/' . $file)));
    }

    #[Test]
    public function it_leaves_the_repository_files_out_of_the_archive(): void
    {
        $attributes = (string) file_get_contents($this->root('.gitattributes'));

        foreach ([
            '/.editorconfig', '/.gitattributes', '/.github', '/.gitignore', '/.markdownlint.json',
            '/.markdownlintignore', '/SECURITY.md', '/assets', '/docs', '/tests',
        ] as $entry) {
            self::assertStringContainsString($entry . ' export-ignore', $attributes);
        }
    }

    #[Test]
    public function it_normalizes_the_line_endings_and_keeps_the_markdown_diffs(): void
    {
        $attributes = (string) file_get_contents($this->root('.gitattributes'));

        foreach (['* text=auto eol=lf', '*.md diff=markdown', '*.png binary'] as $line) {
            self::assertStringContainsString($line, $attributes);
        }
    }

    // Settings that must stay
    // ================

    #[Test]
    public function it_sets_four_spaces_for_php_and_no_line_length_for_markdown(): void
    {
        $editorconfig = (string) file_get_contents($this->root('.editorconfig'));

        self::assertStringContainsString("[*.php]\nindent_size = 4", $editorconfig);

        preg_match('/\[\*\.md\]\n(?<rules>[^\[]*)/', $editorconfig, $markdown);

        self::assertArrayHasKey('rules', $markdown);
        self::assertStringNotContainsString('max_line_length', $markdown['rules']);
    }

    // README and .github/
    // ================

    #[Test]
    public function it_shows_the_icon_in_the_readme_and_not_the_banner(): void
    {
        $readme = (string) file_get_contents($this->root('README.md'));

        self::assertStringContainsString('assets/icon.png', $readme);
        self::assertStringNotContainsString('banner', $readme);
    }

    // Git
    // ================

    #[Test]
    public function it_starts_the_gitignore_with_the_standard_block_of_the_handbook(): void
    {
        $gitignore = (string) file_get_contents($this->root('.gitignore'));

        foreach ([
            '# JetBrains (PhpStorm, WebStorm)', '# Visual Studio Code and derivatives', '# Other editors',
            '# AI assistants', 'AGENTS.md', 'CLAUDE.md', 'GEMINI.md', '*.local.md',
        ] as $text) {
            self::assertStringContainsString($text, $gitignore);
        }
    }

    private function root(string $path = ''): string
    {
        return rtrim(__DIR__ . '/../' . $path, '/');
    }
}
