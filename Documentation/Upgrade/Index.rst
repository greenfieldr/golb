..  include:: /Includes.rst.txt

..  _upgrade:

=========
Upgrading
=========

..  _upgrade-versioning:

Versioning
==========

Since version 13.0.0 the major version of Golb equals the TYPO3 major
version it supports: 13.x.x for TYPO3 13, 14.x.x for TYPO3 14. Minor and
patch versions follow semantic versioning within a line. The changelog is
generated from the commit history and published with every
`GitHub release <https://github.com/greenfieldr/golb/releases>`__.

..  _upgrade-13:

From 3.x / 4.0-dev to 13.0.0
============================

Version 13.0.0 consolidates the TYPO3 12 and 13 development branches and
contains the following breaking changes compared to the 3.x series.

Plugins
    The single "Blogposts" plugin with switchable controller actions was
    replaced by the content types ``golb_listview`` and ``golb_latestview``.
    Plugin settings moved from database columns of ``tt_content`` to a
    FlexForm. Re-create the plugins or migrate the settings yourself.

Database
    The schema is created from the TCA. The columns ``tx_golb_view_count``
    on ``pages`` and ``tx_golb_*`` on ``tt_content`` are no longer used.
    Run the upgrade wizard :guilabel:`Migrates pages field for related
    posts` and :guilabel:`Analyze Database Structure`.

Removed features
    The view count plugin, the backend layout hook, the ``ListPosts``
    ViewHelper and the Fluid widget pagination are gone. The hook that
    set a backend layout for new posts can be replaced with page TSconfig
    (``TCAdefaults.pages.backend_layout``).

Templates
    The templates were rewritten. The list view now renders
    ``paginator.paginatedItems`` and includes the pagination partial.
    Overridden templates of the 3.x series must be adapted.

PHP API
    *   :php:`PostsDemand` no longer implements the removed
        :php:`DemandInterface`; its setters return the demand object.
    *   :php:`Page`: the properties ``alias``, ``urltype``, ``urlScheme``,
        ``noCache`` and ``cruserId`` were removed with their accessors.
        ``isExtendToSubPages()`` is now ``isExtendToSubpages()``. Object
        storage getters never return ``null``.
    *   :php:`Category` has a public constructor and a nullable parent.
    *   :php:`CategoryRepository::findByRelation()` returns an array.
    *   :php:`PageRepository::findPosts()` always sorts the result. Without
        a sorting setting posts are sorted by publish date (fallback
        creation date) descending instead of page tree order.

Behaviour fixes worth knowing
    Sorting by author, creation date, modification date and title, the
    category filter with sub categories and the creation date fallback
    did not work in earlier versions. Listings may change order after the
    update.

..  _upgrade-14:

From 13.x to 14.x
=================

The 14.x line requires TYPO3 14.3 LTS and PHP 8.2 to 8.5. Templates,
TypoScript, plugin settings and the PHP API of the 13.x line are
unchanged, so an update of a 13.x installation needs no migration beyond
the TYPO3 core upgrade itself.

Internal changes made for TYPO3 14:

*   The plugin FlexForms are registered with the ``$flexForm`` argument of
    :php:`ExtensionUtility::registerPlugin()` instead of the removed
    :php:`addPiFlexFormValue()`.
*   The page type is no longer registered with
    :php:`PageDoktypeRegistry->add()`; blog posts allow all record types,
    which is the TCA default.
*   Fluid 5: the default templates and the ViewHelper use strict argument
    types.

Sites that override the ``Pagination`` partial should check it against
the Fluid 5 rules (no CDATA sections, no variable names starting with an
underscore).
