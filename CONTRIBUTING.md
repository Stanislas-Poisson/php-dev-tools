# Contributing

Thank you for helping. php-dev-tools gives the quality tools of the zairakai PHP projects, without Laravel. It is a work in progress.

---

## Development workflow

| Step | Command / Action | Description |
| :--- | :--- | :--- |
| **1. Issue** | Open or pick an issue | One branch and one pull request per issue. |
| **2. Install** | `composer install` | Install the dependencies. |
| **3. Branch** | `git checkout -b feature/#TICKET-name develop` | Create a branch from `develop`. |
| **4. Code** | *(your IDE)* | Keep the change small, and write its test. |
| **5. Check** | `composer quality` | Run the whole quality gate: Pint, PHPStan, Rector, PHP Insights and PHPUnit. |
| **6. Commit** | `git commit -m "type(scope): #TICKET subject"` | Use the [Conventional Commits][conventional-commits] format, in English, 72 characters at most. |
| **7. Push** | `git push origin feature/#TICKET-name` | Push and open a pull request to `develop`. |

A pull request needs a review and is merged with a merge commit.

---

## Checks

The package uses its own command. `composer` calls `php bin/php-dev-tools`, because the package is not installed in itself.

| Command | Tool | Description |
| :--- | :--- | :--- |
| `php bin/php-dev-tools hooks` | Git | Activate the hooks of `hooks/`: the commit message, `quality:fast` before a commit, `quality` before a push. |
| `composer cs` | Pint | Check the code style. `composer cs:fix` fixes it. |
| `composer analyse` | PHPStan | Static analysis at the maximum level with the strict rules, without a baseline. |
| `composer rector` | Rector | Check what Rector would change. `composer rector:fix` applies it. |
| `composer insights` | PHP Insights | The four scores must be 100 %. |
| `composer markdown` | markdownlint | Lint the Markdown files (needs Node.js). |
| `composer test` | PHPUnit | Run the tests. `composer test:coverage` shows the coverage, which must stay at 100 % for `src/`. |
| `composer quality` | All | The whole gate, without the Markdown. |

Do not run `vendor/bin/pint` by hand: it would read only the `pint.json` of the project, not the merged file. A file must not be excluded to hide an error, and an exclusion is explained where it is written.

The `ci` check runs the same commands on PHP 8.3 and 8.4, and must pass before a change reaches `develop` or `main`.

---

## Releasing

Reserved to the maintainer. Tags are plain `X.Y.Z`, signed, and made on `main` only.

1. Merge `develop` into `main` with a pull request, and wait for the CI of `main`.
2. Tag the merge commit and push the tag:

   ```bash
   git checkout main && git pull
   git tag -s 0.1.0 -m "0.1.0"
   git push origin 0.1.0
   ```

3. The `Release` workflow checks that the tag is on `main` and that the CI passed on that commit, then creates the GitHub release. Its notes list the merged pull requests by label and give the `composer require` line.
4. Packagist reads the new tag by itself, once the package is submitted and its GitHub hook is active. The last step of the workflow warns when it does not list the version.

---

## Language

Code, comments, commits, issues and pull requests are in English.

[conventional-commits]: https://www.conventionalcommits.org/
