# Security Policy

## Reporting vulnerabilities

| Channel | Description | Contact / Link |
| :--- | :--- | :--- |
| **Private report** | Preferred channel for sensitive reports. | [Report a vulnerability][advisories] |
| **Issues** | Non-sensitive problems. | [Open an issue][issues] |
| **Email** | Alternative contact. | `security@the-white-rabbits.fr` |

Please **do not disclose a vulnerability publicly** until it has been reviewed and fixed.

---

## Supported versions

| Release | Supported |
| :--- | :--- |
| Latest | Yes |
| Older | No |

Only the latest release receives fixes.

---

## Scope

- php-dev-tools is a development package: it holds the configurations of the tools of a project (Pint, PHPStan, Rector, PHP Insights, markdownlint) and writes `build/pint.json` in the project. It runs no command, has no server and no user account.
- Report any way to make a configuration run a command, or write a file, that the project did not ask for.
- A tool that reports a wrong result, or a rule that is too strict, is not a vulnerability: open an issue.

[advisories]: https://github.com/Stanislas-Poisson/php-dev-tools/security/advisories/new
[issues]: https://github.com/Stanislas-Poisson/php-dev-tools/issues
