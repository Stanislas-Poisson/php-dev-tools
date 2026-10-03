<?php

declare(strict_types=1);

namespace StanislasPoisson\DevTools\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * The Git hooks, run for real in a temporary repository.
 */
final class HooksTest extends TestCase
{
    /**
     * @return iterable<string, array{string, bool}>
     */
    public static function messageProvider(): iterable
    {
        yield 'type, scope, ticket and subject' => ['feat(auth): #123 add user authentication', true];

        yield 'type and subject' => ['chore: update the dependencies', true];

        yield 'a work in progress' => ['WIP', true];

        yield 'a work in progress with text' => ['WIP add something', true];

        yield 'no type' => ['Update stuff', false];

        yield 'the ticket before the type' => ['#7 feat(x): add the thing', false];

        yield 'a type that does not exist' => ['feature(x): add the thing here', false];

        yield 'a subject in capitals' => ['feat(x): Add the thing here', false];

        yield 'a subject that is too short' => ['fix: Short', false];

        yield 'a first line of more than 72 characters' => ['feat(x): ' . str_repeat('a', 70), false];
    }

    /**
     * @return iterable<string, array{string, string, list<string>, string}>
     */
    public static function ticketProvider(): iterable
    {
        yield 'after the type and the scope' => ['feature/#7-name', 'feat(x): add the thing', [], 'feat(x): #7 add the thing'];

        yield 'after the type alone' => ['fix/#12-bug', 'fix: correct the thing', [], 'fix: #12 correct the thing'];

        yield 'with git commit -m' => ['hotfix/#3-now', 'fix(x): correct it', ['message'], 'fix(x): #3 correct it'];

        yield 'with the editor' => ['feature/#7-name', 'feat(x): add the thing', [''], 'feat(x): #7 add the thing'];

        yield 'when the message has the ticket' => ['feature/#7-name', 'feat(x): #7 add the thing', [], 'feat(x): #7 add the thing'];

        yield 'when the message has a ticket that starts the same way' => ['feature/#7-name', 'feat(x): #70 add it', [], 'feat(x): #7 #70 add it'];

        yield 'for a merge' => ['feature/#7-name', 'feat(x): add the thing', ['merge'], 'feat(x): add the thing'];

        yield 'for a squash' => ['feature/#7-name', 'feat(x): add the thing', ['squash'], 'feat(x): add the thing'];

        yield 'for an amend' => ['feature/#7-name', 'feat(x): add the thing', ['commit', 'HEAD'], 'feat(x): add the thing'];

        yield 'when the line is not type and subject' => ['feature/#7-name', 'Add the thing', [], 'Add the thing'];

        yield 'on a branch without a ticket' => ['main', 'feat(x): add the thing', [], 'feat(x): add the thing'];
    }

    public function test_a_message_that_prepare_commit_msg_changed_passes_commit_msg(): void
    {
        [, $first] = $this->hook('prepare-commit-msg', 'feature/#7-name', "feat(x): add the thing that is wanted\n");
        [$code]    = $this->hook('commit-msg', 'feature/#7-name', $first . "\n");

        self::assertSame(0, $code);
    }

    #[DataProvider('messageProvider')]
    public function test_commit_msg_checks_the_format_of_the_message(string $message, bool $accepted): void
    {
        [$code] = $this->hook('commit-msg', 'main', $message . "\n");

        self::assertSame($accepted ? 0 : 1, $code);
    }

    /**
     * @param list<string> $arguments
     */
    #[DataProvider('ticketProvider')]
    public function test_prepare_commit_msg_adds_the_ticket_of_the_branch_after_the_type(
        string $branch,
        string $message,
        array $arguments,
        string $expected,
    ): void {
        [$code, $first] = $this->hook('prepare-commit-msg', $branch, $message . "\n", $arguments);

        self::assertSame(0, $code);
        self::assertSame($expected, $first);
    }

    /**
     * @param list<string> $arguments
     *
     * @return array{int, string} the exit code, and the first line of the message afterwards
     */
    private function hook(string $name, string $branch, string $message, array $arguments = []): array
    {
        $temporaryDirectory = new TemporaryDirectory();
        $file               = $temporaryDirectory->path . '/MESSAGE';
        $temporaryDirectory->write('MESSAGE', $message);

        self::assertSame(0, (new ProcessRunnerQuiet())->run(['git', 'init', '-q', '-b', $branch], $temporaryDirectory->path));

        $code = (new ProcessRunnerQuiet())->run(
            ['bash', __DIR__ . '/../hooks/' . $name, $file, ...$arguments],
            $temporaryDirectory->path,
        );

        $first = strtok((string) file_get_contents($file), "\n");

        return [$code, false === $first ? '' : $first];
    }
}
