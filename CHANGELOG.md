# Changelog

All notable changes to the TYPO3 extension "golb" are documented in this
file. The format is based on conventional commits; the major version
follows the supported TYPO3 major version (13.x.x for TYPO3 13, 14.x.x
for TYPO3 14). The file is regenerated with git-cliff on every release.

## [Unreleased]

### Features

- Support for TYPO3 14.3 LTS and PHP 8.5; the 14.x line requires
  TYPO3 14 (the 13.x line stays on the `typo3v13` branch)

- Default templates with a shared post partial, translatable labels and
  a pagination that uses plugin arguments
- **frontend:** Complete default frontend: teasers with image, flags,
  author image, categories and tags as optional filter links, a card grid
  for the latest view, a plain stylesheet (`Resources/Public/Css/golb.css`)
  added by the layout and switchable with the constant `includeCss`, and
  `show.*` constants to leave single elements out
- **frontend:** Detail rendering of post pages with `lib.golb.post`
  (article with content, related posts) and Open Graph / article meta tags
  added by the `MetaInformationProcessor` option `metaTags`
- **model:** `Page::getFirstMedia()` returns the first file of the
  "Media" field

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

- The Fluid namespace `golb` is no longer registered globally; import it
  with `xmlns:golb` in templates using the related posts ViewHelper
- `Page::setUid()` is removed

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
- TYPO3 13 is no longer supported by the 14.x line
