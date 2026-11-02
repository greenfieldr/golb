# Changelog

All notable changes to the TYPO3 extension "golb" are documented in this
file. The format is based on conventional commits; the major version
follows the supported TYPO3 major version (13.x.x for TYPO3 13, 14.x.x
for TYPO3 14). The file is regenerated with git-cliff on every release.

## [Unreleased]

### Features

- Default templates with a shared post partial, translatable labels and
  a pagination that uses plugin arguments

### Bug fixes

- **model:** Repair domain models and their Extbase mapping: recursive
  `Tag::removePage()`, protected `Category` constructor, unmapped sub
  categories, uninitialised typed properties and unmapped `crdate`,
  `tstamp`, `extendToSubpages`, `lastUpdated` and `newUntil` columns
- **repository:** Correct post sorting (author, creation date,
  modification date, title), duplicate posts in several categories,
  category traversal and the demand object
- **templates:** Render the paginated list and repair pagination links

### Refactoring

- Apply typo3-rector and typo3-fractor TYPO3 13 rule sets
- **controller:** Harden plugin settings handling and service wiring

### Documentation

- Add README and the extension manual (`Documentation/`)

### Build and CI

- Add composer dev tooling (php-cs-fixer, PHPStan, Rector, Fractor,
  testing framework) and QA configuration
- Add GitHub Actions workflows for QA, commit linting and releases

### Breaking changes

- `Page::getAlias()`, `getUrlType()`, `getUrlScheme()`,
  `getCreateUserId()` and their setters are removed;
  `isExtendToSubPages()` is renamed to `isExtendToSubpages()`
- `CategoryRepository::findByRelation()` returns an array instead of a
  `QueryResult`; `DemandInterface` is removed
- `PageRepository::findPosts()` always sorts, by default by publish date
  descending
- `ext_tables.php` and the `addUserTSConfig()` call are removed (the
  page type is registered in the TCA override, user TSconfig in
  `Configuration/user.tsconfig`)
