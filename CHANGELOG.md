# Changelog

All notable changes are listed here. The format follows [Keep a Changelog](https://keepachangelog.com/), and the project follows [Semantic Versioning](https://semver.org/) from the version 0.1.0.

## 0.1.0 - 2026-10-03

The first release.

### Added

- The base configurations of Pint, PHPStan, Rector, PHP Insights and markdownlint, which a project extends ([#3](https://github.com/Stanislas-Poisson/php-dev-tools/issues/3)).
- The command `php-dev-tools`: `cs`, `cs:fix`, `analyse`, `rector`, `rector:fix`, `insights`, `markdown`, `quality`, `quality:fast`, `quality:fix`, `init` and `hooks`.
- The Git hooks `commit-msg`, `pre-commit`, `pre-push` and `prepare-commit-msg`, and a `Makefile.inc`.
