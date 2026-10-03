# The commands of php-dev-tools as targets. Include this file in the Makefile of a project:
#
#     -include vendor/stanislas-poisson/php-dev-tools/Makefile.inc

.PHONY: help
help: ## List the commands
	@grep -hE '^[a-zA-Z_-]+:.*## ' $(MAKEFILE_LIST) | awk -F':.*## ' '{printf "  %-14s %s\n", $$1, $$2}'

.PHONY: hooks
hooks: ## Activate the Git hooks of php-dev-tools
	vendor/bin/php-dev-tools hooks

.PHONY: cs
cs: ## Check the code style with Pint
	vendor/bin/php-dev-tools cs

.PHONY: cs-fix
cs-fix: ## Fix the code style with Pint
	vendor/bin/php-dev-tools cs:fix

.PHONY: analyse
analyse: ## Run PHPStan at the maximum level
	vendor/bin/php-dev-tools analyse

.PHONY: rector
rector: ## Check what Rector would change
	vendor/bin/php-dev-tools rector

.PHONY: rector-fix
rector-fix: ## Apply the changes of Rector
	vendor/bin/php-dev-tools rector:fix

.PHONY: insights
insights: ## Run PHP Insights, which must give 100 % everywhere
	vendor/bin/php-dev-tools insights

.PHONY: markdown
markdown: ## Lint the Markdown files
	vendor/bin/php-dev-tools markdown

.PHONY: test
test: ## Run the tests
	composer test

.PHONY: coverage
coverage: ## Run the tests with the coverage
	composer test:coverage

.PHONY: quality-fast
quality-fast: ## Check the style and run the analysis (the pre-commit hook)
	composer quality:fast

.PHONY: quality
quality: ## Run the whole quality gate (the pre-push hook)
	composer quality

.PHONY: quality-fix
quality-fix: ## Fix what can be fixed: Rector, then Pint
	composer quality:fix
