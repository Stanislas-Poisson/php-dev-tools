# Changelog

All notable changes are listed here. The format follows [Keep a Changelog](https://keepachangelog.com/), and the project follows [Semantic Versioning](https://semver.org/) from the version 0.1.0. See "Versioning" in the README for what the public API is.

## 1.0.0 - 2026-10-03

The first stable version. It holds what the version `0.2.0` was going to hold, which was never tagged.

### Changed

- PHP 8.3 is the lowest version, like in KMark, PPTX-Enigma and French-Postal-Code-Package ([#9](https://github.com/Stanislas-Poisson/php-dev-tools/issues/9)). A project that needs PHP 8.2 stays on `0.1.x`.
- The constants are typed.

### Fixed

- The hook `prepare-commit-msg` put the ticket of the branch in front of the message, which the hook `commit-msg` refused. The ticket now goes after `type(scope): `, also with `git commit -m` ([#11](https://github.com/Stanislas-Poisson/php-dev-tools/issues/11)).

## 0.1.0 - 2026-10-03

The first release.

### Added

- The base configurations of Pint, PHPStan, Rector, PHP Insights and markdownlint, which a project extends ([#3](https://github.com/Stanislas-Poisson/php-dev-tools/issues/3)).
- The command `php-dev-tools`: `cs`, `cs:fix`, `analyse`, `rector`, `rector:fix`, `insights`, `markdown`, `quality`, `quality:fast`, `quality:fix`, `init` and `hooks`.
- The Git hooks `commit-msg`, `pre-commit`, `pre-push` and `prepare-commit-msg`, and a `Makefile.inc`.
