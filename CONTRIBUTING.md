# Contributing

Thank you for helping. php-dev-tools gives the quality settings of the zairakai PHP projects, without Laravel. It is a work in progress.

---

## Development workflow

| Step | Command / Action | Description |
| :--- | :--- | :--- |
| **1. Issue** | Open or pick an issue | One branch and one pull request per issue. |
| **2. Install** | `composer install` | Install the dependencies. |
| **3. Branch** | `git checkout -b feature/#TICKET-name develop` | Create a branch from `develop`. |
| **4. Code** | *(your IDE)* | Keep the change small, and write its test. |
| **5. Check** | `composer quality` | Run the whole quality gate: Pint, PHPStan, Rector, PHP Insights and PHPUnit. |
| **6. Commit** | `git commit -m "type(scope): #TICKET subject"` | Use the format of the [zairakai handbook][handbook-git-rules], in English, 50 characters at most for the subject (after the type, the scope and the ticket). |
| **7. Push** | `git push origin feature/#TICKET-name` | Push and open a pull request to `develop`. |

A pull request needs a review and is merged with a merge commit.

---

## Checks

The package runs the tools with the Composer scripts, on its own base files.

| Command | Tool | Description |
| :--- | :--- | :--- |
| `composer cs` | Pint | Check the code style. `composer cs:fix` fixes it (also `cs-fix`). |
| `composer analyse` | PHPStan | Static analysis at the maximum level with the strict rules, without a baseline. |
| `composer rector` | Rector | Check what Rector would change. `composer rector:fix` applies it (also `rector-fix`). |
| `composer insights` | PHP Insights | The four scores must be 100 %. |
| `composer markdown` | markdownlint | Lint the Markdown files (needs Node.js). |
| `composer test` | PHPUnit | Run the tests. `composer test:coverage` shows the coverage, which must stay at 100 % for `src/`. |
| `composer quality` | All | The whole gate, without the Markdown. `composer quality:fix` applies the fixes of Rector, then of Pint (also `quality-fix`). |

A file must not be excluded to hide an error, and an exclusion is explained where it is written.

The `ci` check runs the same commands on PHP 8.4, and must pass before a change reaches `develop` or `main`.

---

## Dependencies held back

Some dependencies stay on an older version because another package does not accept the new one yet. They are listed in one place, the [dependency policy](https://gitlab.com/zairakai/dependency-policy) (`php.json`), with the reason and the condition that lifts the block. Nothing is written here by hand:

- The workflow `Policy sync` keeps the ignore rules of `.github/dependabot.yml` (between the markers `dependency-policy (composer)`) equal to the policy, with a signed pull request.
- The check `Dependency policy` fails a pull request that updates a package of the policy, in `composer.json` or `composer.lock`.
- When the peers accept the new version, the policy lifts the entry by itself, the next sync removes the rule, and Dependabot proposes the update.

---

## Releasing

Reserved to the maintainer. Tags are plain `X.Y.Z`, signed, and made on `main` only.

1. Merge `develop` into `main` with a pull request, and wait for the CI of `main`.
2. Tag the merge commit and push the tag:

   ```bash
   git checkout main && git pull
   git tag -s 1.0.0 -m "1.0.0"
   git push origin 1.0.0
   ```

3. The `Release` workflow checks that the tag is on `main` and that the CI passed on that commit, then creates the GitHub release. Its notes list the merged pull requests by label and give the `composer require` line.
4. Packagist reads the new tag by itself, once the package is submitted and its GitHub hook is active. The last step of the workflow warns when it does not list the version.

---

## Language

Code, comments, commits, issues and pull requests are in English.

[handbook-git-rules]: https://gitlab.com/zairakai/handbook/-/blob/main/policies/git-rules.md
