..  include:: /Includes.rst.txt

..  _configuration:

=============
Configuration
=============

..  _configuration-typoscript:

TypoScript
==========

The static TypoScript "Golb" defines the view paths and settings of both
plugins under :typoscript:`plugin.tx_golb`. The plugin specific objects
:typoscript:`plugin.tx_golb_listview` and
:typoscript:`plugin.tx_golb_latestview` are copied from it, so settings
can be changed for both plugins at once or for a single plugin.

..  confval:: plugin.tx_golb.view.templateRootPaths
    :name: typoscript-templateRootPaths
    :type: array

    Template paths. Index ``0`` holds the extension defaults. Add your own
    path with a higher index to override the templates, see
    :ref:`templating`.

..  confval:: plugin.tx_golb.view.partialRootPaths
    :name: typoscript-partialRootPaths
    :type: array

    Partial paths, same mechanism as the template paths.

..  confval:: plugin.tx_golb.view.layoutRootPaths
    :name: typoscript-layoutRootPaths
    :type: array

    Layout paths, same mechanism as the template paths.

..  confval:: plugin.tx_golb.settings.includeCss
    :name: typoscript-includeCss
    :type: boolean (constant)
    :default: ``1``

    Whether the default layout adds the stylesheets of
    :ref:`settings.css <typoscript-css>` to the page. Disable it to style
    the markup yourself.

..  confval:: plugin.tx_golb.settings.dateFormat
    :name: typoscript-dateFormat
    :type: string (constant)
    :default: ``%d.%m.%Y``

    Date format used by the default templates, see the
    :ref:`f:format.date <t3viewhelper:typo3-fluid-format-date>` ViewHelper.

..  confval:: plugin.tx_golb.settings.css
    :name: typoscript-css
    :type: array

    Stylesheets the default layout adds to the page while
    :ref:`includeCss <typoscript-includeCss>` is enabled, by default
    ``golb = EXT:golb/Resources/Public/Css/golb.css``. Remove or add
    entries to replace or extend the default styles, see
    :ref:`templating-css`.

..  confval:: plugin.tx_golb.settings.filterLinks.namespace
    :name: typoscript-filterLinks-namespace
    :type: string
    :default: ``tx_golb_listview``

    Plugin namespace of the demand arguments the
    :ref:`filter links <templating-filter-links>` carry.

The object :typoscript:`lib.golb.post` renders the detail view of a post
page, see :ref:`templating-detail`. It copies
:typoscript:`plugin.tx_golb.settings`, so settings that should apply to
the plugins and the detail rendering alike are best set as constants.

..  _configuration-constants:

Constants
=========

The following constants are available in the constant editor (category
"plugin.tx_golb") and are used by the default templates:

..  list-table::
    :header-rows: 1

    *   -   Constant
        -   Default
        -   Description
    *   -   ``settings.includeCss``
        -   ``1``
        -   Adds the default stylesheet to the page, see
            :ref:`templating-css`
    *   -   ``settings.dateFormat``
        -   ``%d.%m.%Y``
        -   Date format of the publish date, see the
            :ref:`f:format.date <t3viewhelper:typo3-fluid-format-date>`
            ViewHelper
    *   -   ``settings.show.image``
        -   ``1``
        -   The first file of the page field "Media" as teaser and
            detail image
    *   -   ``settings.show.authorImage``
        -   ``1``
        -   The author image next to the author name
    *   -   ``settings.show.abstract``
        -   ``1``
        -   The abstract of the post
    *   -   ``settings.show.categories``
        -   ``1``
        -   The categories of the post
    *   -   ``settings.show.tags``
        -   ``1``
        -   The tags of the post
    *   -   ``settings.show.flags``
        -   ``1``
        -   The "Top post" and "Archived" badges
    *   -   ``settings.show.related``
        -   ``1``
        -   The related posts below the detail rendering
    *   -   ``settings.imageWidth.teaser``
        -   ``640``
        -   Width of the teaser image in pixels
    *   -   ``settings.imageWidth.detail``
        -   ``1280``
        -   Width of the image in the detail rendering in pixels
    *   -   ``settings.imageWidth.author``
        -   ``48``
        -   Width and height of the author image in pixels
    *   -   ``settings.filterLinks.enabled``
        -   ``0``
        -   Link categories and tags to the filtered List view, see
            :ref:`templating-filter-links`
    *   -   ``settings.filterLinks.pageUid``
        -   ``0``
        -   Page with the List view plugin the filter links point to,
            ``0`` for the current page
    *   -   ``settings.related.limit``
        -   ``3``
        -   Number of related posts found by shared tags
    *   -   ``settings.related.pages``
        -   empty
        -   Comma separated root page uids the related posts are collected
            from, empty for the parent page of the post

The TypoScript constants :typoscript:`plugin.tx_golb.view.templateRootPath`,
:typoscript:`partialRootPath` and :typoscript:`layoutRootPath` are
registered at index ``10`` of the respective paths:

..  code-block:: typoscript
    :caption: Constants of the site package

    plugin.tx_golb.view {
        templateRootPath = EXT:site_package/Resources/Private/Extensions/Golb/Templates/
        partialRootPath = EXT:site_package/Resources/Private/Extensions/Golb/Partials/
        layoutRootPath = EXT:site_package/Resources/Private/Extensions/Golb/Layouts/
    }

..  _configuration-flexform:

Plugin settings
===============

The FlexForm settings of the plugins are available as
:typoscript:`settings` in the controller and the templates. They can also
be set in TypoScript as defaults, FlexForm values take precedence:

..  code-block:: typoscript

    plugin.tx_golb_listview.settings {
        itemsPerPage = 20
        sorting = date
        sortingDirection = DESC
    }

..  list-table::
    :header-rows: 1

    *   -   Setting
        -   Values
    *   -   ``sorting``
        -   ``date`` (default), ``crdate``, ``tstamp``, ``title``, ``author``
    *   -   ``sortingDirection``
        -   ``ASC``, ``DESC`` (default)
    *   -   ``limit``
        -   Integer, ``0`` for no limit (Latest view)
    *   -   ``itemsPerPage``
        -   Integer (List view), default ``10``
    *   -   ``offset``
        -   Integer
    *   -   ``exclude``
        -   Comma separated page uids
    *   -   ``archived``
        -   empty (all), ``archived``, ``nonArchived``
    *   -   ``topPosts``
        -   empty (all), ``topPosts``, ``nonTopPosts``
    *   -   ``allowDemandOverwrite``
        -   ``0`` or ``1``

..  _configuration-demand:

Filtering by URL parameters
===========================

If :guilabel:`Allow demand overwrite` is enabled, the listing can be
changed with request arguments in the plugin namespace
(``tx_golb_listview`` or ``tx_golb_latestview``). The arguments are mapped
onto the demand object (see :ref:`developer-demand`); values that are not
given fall back to the plugin settings.

..  list-table::
    :header-rows: 1

    *   -   Argument
        -   Example
    *   -   ``demand[tag]``
        -   ``?tx_golb_listview[demand][tag]=3`` lists posts with tag 3
    *   -   ``demand[tags]``
        -   ``?tx_golb_listview[demand][tags][0]=3&tx_golb_listview[demand][tags][1]=4``
    *   -   ``demand[category]``
        -   ``?tx_golb_listview[demand][category]=7`` lists posts in
            category 7 or one of its sub categories
    *   -   ``demand[currentPage]``
        -   Page of the pagination, generated by the pagination partial
    *   -   ``demand[order]``, ``demand[orderDirection]``
        -   Sorting, same values as the plugin settings
    *   -   ``demand[archived]``, ``demand[topPost]``, ...
        -   Boolean flags

For speaking URLs map the arguments with the
:ref:`routing configuration <t3coreapi:routing-advanced-routing-configuration>`
of the site, for example:

..  code-block:: yaml
    :caption: config/sites/main/config.yaml

    routeEnhancers:
      GolbTag:
        type: Extbase
        extension: Golb
        plugin: ListView
        routes:
          - routePath: '/tag/{tag}'
            _controller: 'Blog::list'
            _arguments:
              tag: 'demand/tag'
          - routePath: '/page/{page}'
            _controller: 'Blog::list'
            _arguments:
              page: 'demand/currentPage'
        defaultController: 'Blog::list'
        aspects:
          tag:
            type: PersistedAliasMapper
            tableName: tx_golb_domain_model_tag
            routeFieldName: slug
          page:
            type: StaticRangeMapper
            start: '1'
            end: '100'

..  _configuration-tsconfig:

TSconfig
========

The extension ships :file:`Configuration/page.tsconfig` (content element
wizard entries for the plugins) and :file:`Configuration/user.tsconfig`
(offers the "Blog post" page type in the drag area of the page tree).
Both are loaded automatically.

..  _configuration-doktype:

Page type
=========

The "Blog post" page type uses doktype ``41``, available as the constant
:php:`\Greenfieldr\Golb\Constants::BLOG_POST_DOKTYPE`. Blog posts behave
like standard pages: they are shown in menus unless hidden in navigation,
can be translated and use backend layouts.
