..  include:: /Includes.rst.txt

..  _introduction:

============
Introduction
============

..  _what-it-does:

What does it do?
================

Most blog extensions store posts in their own database table and render
them through a detail plugin. Golb takes a different approach, "the page
way":

*   A **blog post is a page** with the custom page type (doktype) ``41``
    labelled "Blog post". Editors create posts in the page tree, exactly
    like normal pages.
*   The **content of a post** is made of regular content elements. Backend
    layouts, grids and every content element type of the installation
    work out of the box.
*   **Metadata** lives on the page record: publish date, author, author
    image, system categories, tags, related posts, an "archived" and a
    "top post" flag.
*   **Listings** are provided by two plugins: *List view* (paginated) and
    *Latest view* (limited number of posts). Both collect the blog posts
    recursively below the selected root pages and can filter by
    categories, tags, archive and top post state.
*   The current post can be exposed to the page template with a **data
    processor**, and a **ViewHelper** finds related posts by shared tags.

Because posts are pages, everything TYPO3 offers for pages applies:
URL routing, translations, workspaces, access restrictions, SEO fields,
caching and the page module.

..  _features:

Features
========

*   Custom page type "Blog post" with its own page tree icon
*   Publish date with fallback to the creation date
*   Tags (own record type with slug, translatable) and system categories
    including sub categories
*   Related posts, either selected by hand or found by shared tags
*   Archived and top posts as filter criteria
*   List view with pagination and Latest view with limit
*   Sorting by publish date, creation date, modification date, title or
    author
*   Filtering by URL parameters (categories, tags, page) if the plugin
    allows it
*   Overridable Fluid templates with sensible defaults

..  _compatibility:

Compatibility
=============

..  list-table::
    :header-rows: 1

    *   -   Golb version
        -   TYPO3 version
        -   PHP version
    *   -   13.x
        -   13.4 LTS
        -   8.2 - 8.4
    *   -   14.x
        -   14.x
        -   8.2 - 8.5

The major version of the extension follows the supported TYPO3 major
version. Bug fixes are released for both lines.

..  _support:

Support and contributions
=========================

Source code, issues and pull requests are hosted on
`GitHub <https://github.com/greenfieldr/golb>`__. Commit messages follow
the `Conventional Commits <https://www.conventionalcommits.org/>`__
specification, which is enforced by the CI pipeline.
