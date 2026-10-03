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

| Version | Supported |
| :--- | :--- |
| `0.2.x` | Yes |

While the package is `0.x`, only the latest minor version receives fixes.

---

## Scope

- php-dev-tools is a development tool: it runs the tools of a project (Pint, PHPStan, Rector, PHP Insights, markdownlint) with `proc_open`, writes `build/pint.json` in the project, and its Git hooks run the Composer scripts of the project. It has no server and no user account.
- It runs the commands without a shell: the arguments are not read by one. It reads the `pint.json` of the project, and it never executes a file other than the tools and the scripts of the project.
- Report any way to make it run a command, or write a file, that the project did not ask for.
- A tool that reports a wrong result, or a rule that is too strict, is not a vulnerability: open an issue.

[advisories]: https://github.com/Stanislas-Poisson/php-dev-tools/security/advisories/new
[issues]: https://github.com/Stanislas-Poisson/php-dev-tools/issues
