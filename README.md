# Golb - Blog The Page Way

[![CI](https://github.com/greenfieldr/golb/actions/workflows/ci.yml/badge.svg)](https://github.com/greenfieldr/golb/actions/workflows/ci.yml)
[![Latest release](https://img.shields.io/github/v/release/greenfieldr/golb)](https://github.com/greenfieldr/golb/releases)
[![License: GPL-3.0-or-later](https://img.shields.io/badge/license-GPL--3.0--or--later-blue.svg)](LICENSE)

Golb is a TYPO3 blog extension that uses **pages as blog posts**. A post is a
page of the custom page type "Blog post": it is edited in the page tree, built
from regular content elements and carries its metadata (publish date, author,
categories, tags, related posts, archived and top post flags) on the page
record. Two plugins list the posts: a paginated *List view* and a limited
*Latest view*.

## Compatibility

| Golb  | TYPO3    | PHP       |
|-------|----------|-----------|
| 13.x  | 13.4 LTS | 8.2 - 8.4 |
| 14.x  | 14.x     | 8.2 - 8.5 |

The major version follows the supported TYPO3 major version.

## Installation

```bash
composer require greenfieldr/golb
```

Then run "Analyze Database Structure", include the static TypoScript "Golb",
create a blog root page with posts of type "Blog post" and add a *List view*
or *Latest view* plugin. The full documentation is in the
[Documentation](Documentation/) folder and rendered on
[docs.typo3.org](https://docs.typo3.org/p/greenfieldr/golb/main/en-us/).

## Development

```bash
composer install
composer ci     # coding standards, PHPStan, Rector, Fractor, unit and functional tests
composer fix    # apply automated fixes
```

Commit messages follow [Conventional Commits](https://www.conventionalcommits.org/).
Releases are tagged `13.x.x` (TYPO3 13) and `14.x.x` (TYPO3 14); the changelog
is generated from the commit history with git-cliff.

## License

GPL-3.0-or-later, see [LICENSE](LICENSE).
