# Contributing

Contributions are welcome. Please follow the guidelines below.

## Reporting issues

Open an issue on [GitHub](https://github.com/devxisas/laravel-qrcode/issues) with a clear description, steps to reproduce, and the PHP / Laravel versions you are using.

## Pull requests

1. Fork the repository and create a branch from `main`.
2. Run `composer install` to install dependencies.
3. Make your changes, keeping code style consistent:
   ```bash
   composer format
   ```
4. Add or update tests to cover your change:
   ```bash
   composer test
   ```
5. Run static analysis:
   ```bash
   composer analyse
   ```
6. Commit using [Conventional Commits](https://www.conventionalcommits.org/) — `feat:`, `fix:`, `docs:`, `chore:`, etc.
7. Open a pull request against `main`.

## Commit message format

This project uses [Conventional Commits](https://www.conventionalcommits.org/) to drive automated changelogs and version bumps via [release-please](https://github.com/googleapis/release-please).

| Type    | When to use                                  |
|---------|----------------------------------------------|
| `feat`  | A new feature                                |
| `fix`   | A bug fix                                    |
| `docs`  | Documentation changes only                  |
| `perf`  | Performance improvements                     |
| `ci`    | CI/CD workflow changes                       |
| `chore` | Maintenance (deps, build, tooling)           |

Breaking changes must include `BREAKING CHANGE:` in the commit footer, or use `feat!:` / `fix!:`.
