# php-dev-tools

The quality tools of the zairakai PHP projects, without Laravel: Pint, PHPStan, Rector, PHP Insights, markdownlint and the Git hooks, as a base that a project extends.

## The principle

A project does not copy the rules of the tools. It has its own small configuration files, which **extend** the rules that live in `vendor/`. The rules are then always the ones of the installed version of this package, and the project only writes what is specific to it: an exclusion that is justified, a path, a rule it changes.

| File of the project | What it does |
| :--- | :--- |
| `pint.json` | Only the rules and the exclusions that the project changes. They are merged with the base rules before Pint runs. |
| `phpstan.neon.dist` | `includes` the base file, then sets the paths and the ignored errors of the project. |
| `rector.php` | Calls `Rector::configure()` with the version of PHP and the directories. |
| `phpinsights.php` | Calls `Insights::config()` with the overrides of the project. |
| `.markdownlint.json` | `extends` the base file. |
| `Makefile` | Includes `Makefile.inc` and adds the targets of the project. |

## Requirements

PHP 8.3 or higher, like KMark, PPTX-Enigma and French-Postal-Code-Package.

## Installation

```sh
composer require --dev stanislas-poisson/php-dev-tools
composer config allow-plugins.dealerdirect/phpcodesniffer-composer-installer true
vendor/bin/php-dev-tools init     # creates the files above, never replaces one
vendor/bin/php-dev-tools hooks    # activates the Git hooks
```

PHP Insights needs the `dealerdirect/phpcodesniffer-composer-installer` plugin: the second command allows it. Then add the scripts to the `composer.json` of the project, so that `composer cs`, `composer quality` and the hooks work:

```json
"scripts": {
    "analyse": "php-dev-tools analyse",
    "cs": "php-dev-tools cs",
    "cs:fix": "php-dev-tools cs:fix",
    "insights": "php-dev-tools insights",
    "markdown": "php-dev-tools markdown",
    "quality": ["php-dev-tools quality", "@test"],
    "quality:fast": "php-dev-tools quality:fast",
    "quality:fix": "php-dev-tools quality:fix",
    "rector": "php-dev-tools rector",
    "rector:fix": "php-dev-tools rector:fix",
    "test": "phpunit"
}
```

## The command

`vendor/bin/php-dev-tools <command>`

| Command | Description |
| :--- | :--- |
| `cs`, `cs:fix` | Check, or fix, the code style with Pint. |
| `analyse` | PHPStan at the maximum level with the strict rules and the PHPUnit extension, without a baseline. |
| `rector`, `rector:fix` | Check, or apply, what Rector changes. |
| `insights` | PHP Insights, which must give 100 % for the code, the complexity, the architecture and the style. |
| `markdown` | markdownlint on the Markdown files (needs Node.js). |
| `quality` | `cs`, `analyse`, `rector` and `insights`. |
| `quality:fast` | `cs` and `analyse`: the pre-commit hook. |
| `quality:fix` | `rector:fix`, then `cs:fix`. |
| `init` | Create the files that extend the base ones. |
| `hooks` | Activate the Git hooks. |

The commands run from the root of the project. A command that fails stops the ones that follow, and gives its exit code. Pint does not use its cache: a check never relies on an old result.

## Extending the base

### Pint

`pint.json` of the project holds only what it changes. Its `rules` replace the ones of the base by name, and the `exclude` of its `finder` is added to the one of the base (`build` and `vendor` are always excluded):

```json
{
  "rules": {
    "yoda_style": false
  },
  "finder": {
    "exclude": ["data"]
  }
}
```

The merged file is written in `build/pint.json`. Do not run `vendor/bin/pint` by hand: it would read only the file of the project.

### PHPStan

```neon
includes:
    - vendor/stanislas-poisson/php-dev-tools/config/phpstan.neon

parameters:
    paths:
        - src
        - tests
```

### Rector

```php
use StanislasPoisson\DevTools\Rector;

return Rector::configure(__DIR__, php: '8.3')
    ->withSkip([SomeRector::class => [__DIR__ . '/src/Entity']]);
```

`Rector::configure()` returns the builder of Rector, with the sets of `laravel-dev-tools` for the version of PHP and without the Laravel ones. The directories are `src` and `tests`, and the ones that do not exist are left out.

### PHP Insights

```php
use StanislasPoisson\DevTools\Insights;

return Insights::config([
    'exclude' => ['config/reference.php'],
    'config'  => [
        SomeSniff::class => ['exclude' => ['src/Models']],
    ],
]);
```

The lists `remove` and `exclude` are added to the ones of the base. The keys of `add`, `config` and `requirements` replace the ones of the base. Any other key replaces the base. The base asks for 100 % everywhere, and removes the rules that contradict Pint.

A file must not be excluded to hide an error: write why next to each exclusion.

### markdownlint

```json
{
  "extends": "./vendor/stanislas-poisson/php-dev-tools/config/markdownlint.json"
}
```

## The Git hooks

`vendor/bin/php-dev-tools hooks` sets `core.hooksPath` to the `hooks` directory of this package, so that the hooks are always the ones of the installed version.

| Hook | What it does |
| :--- | :--- |
| `commit-msg` | The message follows `type(scope): #TICKET subject`: the subject starts in lowercase and has at least 10 characters, and the first line has 72 characters at most. `WIP` is always accepted. |
| `prepare-commit-msg` | Adds the ticket of the branch (`feature/#12-name`) to the message. |
| `pre-commit` | `composer quality:fast`. |
| `pre-push` | `composer quality`. |

`git commit --no-verify` and `git push --no-verify` skip them.

## Makefile

`Makefile.inc` gives the targets `help`, `hooks`, `cs`, `cs-fix`, `analyse`, `rector`, `rector-fix`, `insights`, `markdown`, `test`, `coverage`, `quality-fast`, `quality` and `quality-fix`. Include it in the `Makefile` of the project:

```make
-include vendor/stanislas-poisson/php-dev-tools/Makefile.inc
```

## Known limits

- **The tools come with this package**: a project cannot choose their version.
- **The scripts of `composer.json` are written by hand**: `init` does not edit this file.

## Versioning

The package follows [Semantic Versioning](https://semver.org/). Require it with `^1.0`.

**A major version** changes what a project writes or calls:

- the names and the options of the commands of `php-dev-tools`,
- `Rector::configure()` and `Insights::config()`, with the keys that `Insights::config()` merges,
- the files that `init` creates, and how the `pint.json` of a project is merged,
- the paths of the base files in `vendor/` that a project includes or extends,
- the names of the Git hooks, and the format of a commit message that they ask for,
- the lowest version of PHP.

**A minor version** can add a command, a hook or an option, and **can add or tighten a rule** of the base, or raise the version of a tool: a project that was green can turn red. The changelog lists these changes, and a project that does not want to follow can keep the version it has.

**A patch version** fixes a defect without changing what the tools ask for.

## Development

```sh
composer install
php bin/php-dev-tools hooks
composer quality   # the package passes its own tools
```

See [CONTRIBUTING.md](CONTRIBUTING.md) for the details.

## Roadmap

1. The configurations, the command and the hooks ([#3](https://github.com/Stanislas-Poisson/php-dev-tools/issues/3)): done.
2. The community files ([#2](https://github.com/Stanislas-Poisson/php-dev-tools/issues/2)): done.
3. Used in KMark, PPTX-Enigma and French-Postal-Code-Package: done.
4. The first release, `0.1.0` ([#6](https://github.com/Stanislas-Poisson/php-dev-tools/issues/6)): done.
5. PHP 8.3 as the lowest version, with the other projects ([#9](https://github.com/Stanislas-Poisson/php-dev-tools/issues/9)): done.
6. The first stable version, `1.0.0` ([#12](https://github.com/Stanislas-Poisson/php-dev-tools/issues/12)).

## License

[MIT](LICENSE). Copyright (c) 2026 Stanislas Poisson.
