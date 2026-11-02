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
    *   The Fluid namespace ``golb`` is no longer registered globally; import
        it in the templates that use the ViewHelper.
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

The 14.x line targets TYPO3 14. The templates, TypoScript and the PHP API
stay compatible; only the TYPO3 and PHP requirements change. Details are
listed in the release notes of 14.0.0.
