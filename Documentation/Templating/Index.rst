..  include:: /Includes.rst.txt

..  _templating:

==========
Templating
==========

..  _templating-templates:

Templates
=========

The default templates render complete, styled lists and a detail view:
teasers with image, flags ("Top post", "Archived"), title, date, author
(with image), abstract, categories and tags; a paginated list; a card grid
for the latest posts; and the article of a post page with its content and
related posts. The markup uses BEM style classes (``golb-post``,
``golb-post__title``, ``golb-post--teaser``, ...) and is styled by a
plain stylesheet without framework or colours, see :ref:`templating-css`.

..  code-block:: text

    Resources/Private/
    ├── Layouts/Default.html         Adds the stylesheets and wraps the output in <div class="golb">
    ├── Partials/
    │   ├── Pagination.html          Pagination of the list view
    │   ├── Post.html                One post as teaser in a list
    │   └── Post/
    │       ├── Flags.html           "Top post" and "Archived" badges
    │       ├── Meta.html            Publish date and author with image
    │       ├── Related.html         Related posts of the detail rendering
    │       └── Terms.html           Categories and tags, optionally as filter links
    └── Templates/
        ├── Blog/
        │   ├── Latest.html          Latest view plugin (card grid)
        │   └── List.html            List view plugin (list with pagination)
        └── Page/Post.html           Detail rendering of a post page (lib.golb.post)

Resources/Public/Css/golb.css        Default stylesheet

To change the markup copy the files you need to your site package and
register the path as described in :ref:`configuration-typoscript`; a
file found in your path replaces the default file of the same name. The
elements of the default templates can also be switched off one by one
with the ``show`` settings, see :ref:`configuration-constants`.

Variables available in the plugin templates:

..  list-table::
    :header-rows: 1

    *   -   Variable
        -   Template
        -   Description
    *   -   ``posts``
        -   both
        -   Array of :php:`\Greenfieldr\Golb\Domain\Model\Page` objects.
            In the list view this is the complete, unpaginated result.
    *   -   ``paginator``
        -   List
        -   :php:`\TYPO3\CMS\Core\Pagination\ArrayPaginator`, use
            ``paginator.paginatedItems`` for the posts of the current page
    *   -   ``pagination``
        -   List
        -   :php:`\TYPO3\CMS\Core\Pagination\SimplePagination`
    *   -   ``demand``
        -   both
        -   The demand object, e.g. ``demand.currentPage`` or
            ``demand.tag``; the list view shows a "Show all posts" link
            while a tag or category filter is active
    *   -   ``contentElementData``
        -   both
        -   The tt_content record of the plugin
    *   -   ``settings``
        -   both
        -   Plugin settings (FlexForm merged with TypoScript)

..  _templating-page-model:

The post object
===============

A post is a :php:`\Greenfieldr\Golb\Domain\Model\Page`. Frequently used
properties:

..  list-table::
    :header-rows: 1

    *   -   Property
        -   Description
    *   -   ``uid``, ``title``, ``subtitle``, ``abstract``, ``description``
        -   Standard page fields
    *   -   ``navigationTitle``
        -   Navigation title with fallback to the title
    *   -   ``effectiveDate``
        -   Publish date, or creation date if no publish date is set
    *   -   ``publishDate``, ``creationDate``, ``tstamp``
        -   Timestamps
    *   -   ``author``, ``authorEmail``, ``authorImage``
        -   Author fields; ``authorImage`` is a file reference or null
    *   -   ``media``, ``firstMedia``
        -   File references of the page "Media" field, and the first one
            (or null), used as teaser and detail image
    *   -   ``pid``
        -   Uid of the parent page, the default scope of the related posts
    *   -   ``categories``, ``tags``
        -   Object storages of categories and tags
    *   -   ``relatedPages``
        -   Hand-picked related posts
    *   -   ``archived``, ``topPost``
        -   Boolean flags
    *   -   ``contentElements``
        -   Content elements of the post (lazy)

Links to a post are regular page links:

..  code-block:: html

    <f:link.page pageUid="{post.uid}">{post.title}</f:link.page>

..  _templating-detail:

Rendering the post page
=======================

A blog post is rendered by the page template of the site. The extension
ships the TypoScript object :typoscript:`lib.golb.post`, a FLUIDTEMPLATE
that renders the complete article of the current page: flags, title,
subtitle, publish date and author, the first media file, the abstract,
the page content (``styles.content.get`` of fluid_styled_content), the
categories and tags, and the related posts. It also adds the Open Graph
and article meta tags to the page header (``og:type``,
``article:published_time``, ``article:modified_time``,
``article:author``, ``article:section``, ``article:tag``); properties the
site has set already are kept.

Insert it in the page template of the site in place of the content:

..  code-block:: html

    <f:cObject typoscriptObjectPath="lib.golb.post" data="{data}"/>

or let TypoScript replace the content of blog post pages:

..  code-block:: typoscript

    [page["doktype"] == 41]
    page.10.variables.content =< lib.golb.post
    [END]

The template :file:`Templates/Page/Post.html` and the partials it uses
are overridden like the plugin templates (the ``Page/`` folder below
:typoscript:`plugin.tx_golb.view.templateRootPath`); the object copies
:typoscript:`plugin.tx_golb.settings`, so the ``show`` settings, the
image sizes and the filter links apply to it as well.

If you prefer to build the detail view yourself, add the data processor
to the page object and work with the post object:

..  code-block:: typoscript

    page.10 = PAGEVIEW
    page.10 {
        paths.10 = EXT:site_package/Resources/Private/Templates/
        dataProcessing {
            10 = Greenfieldr\Golb\DataProcessing\MetaInformationProcessor
            10.as = blogPost
            10.metaTags = 1
        }
    }

``blogPost`` is then the :php:`Page` object of the current page (also for
pages that are not blog posts, check ``blogPost.doktype`` if necessary);
``metaTags`` adds the meta tags described above for blog post pages.

..  code-block:: html

    <f:if condition="{blogPost.doktype} == 41">
        <p class="post-meta">
            <f:format.date format="d.m.Y">@{blogPost.effectiveDate}</f:format.date>
            <f:if condition="{blogPost.author}">, {blogPost.author}</f:if>
        </p>
    </f:if>

..  _templating-related:

Related posts
=============

The partial :file:`Partials/Post/Related.html` of the detail rendering
shows the hand-picked related posts of the page record if there are any,
otherwise the posts that share the most tags with the current post. The
latter are collected below the pages of the setting
:typoscript:`plugin.tx_golb.settings.related.pages` (by default the parent
page of the post), limited to :typoscript:`related.limit`.

The lookup is done by the ViewHelper :html:`<golb:relatedPosts>`, which
can be used in any template. Import the namespace with
``xmlns:golb="http://typo3.org/ns/Greenfieldr/Golb/ViewHelpers"``
or ``{namespace golb=Greenfieldr\Golb\ViewHelpers}``.

..  code-block:: html

    <golb:relatedPosts posts="{0: 12}" currentPost="{blogPost}" limit="3" as="relatedPosts">
        <f:for each="{relatedPosts}" as="related">
            <f:link.page pageUid="{related.post.uid}">{related.post.title}</f:link.page>
            ({related.amount} shared tags)
        </f:for>
    </golb:relatedPosts>

..  list-table::
    :header-rows: 1

    *   -   Argument
        -   Description
    *   -   ``posts``
        -   Array of root page uids to search in (required)
    *   -   ``currentPost``
        -   The current :php:`Page` object (required)
    *   -   ``limit``
        -   Number of related posts, default ``3``
    *   -   ``as``
        -   Variable name, default ``relatedPosts``

Each entry of the result is an array with the keys ``post`` (the
:php:`Page`) and ``amount`` (number of shared tags).

..  _templating-filter-links:

Filter links
============

With the constant :typoscript:`plugin.tx_golb.settings.filterLinks.enabled`
the categories and tags of the teasers and the detail rendering link to
the List view plugin, filtered by that term. The links point to the page
:typoscript:`filterLinks.pageUid` (by default the current page) and carry
the demand argument of the plugin namespace, e.g.
``tx_golb_listview[demand][tag]=3``. The List view plugin on the target
page has to allow demand overwrites, see :ref:`configuration-demand`.

..  _templating-pagination:

Pagination
==========

The pagination partial renders links with the argument
``demand[currentPage]`` of the plugin. Other demand arguments of the
current request (for example a tag filter) are not carried over by the
default partial; extend it with :html:`<f:link.action addQueryString="untrusted">`
or explicit arguments if you need that.

The labels are translated with the keys ``pagination.first``,
``pagination.previous``, ``pagination.next`` and ``pagination.last`` of
:file:`Resources/Private/Language/locallang.xlf`.

..  _templating-css:

Styling
=======

The default stylesheet :file:`Resources/Public/Css/golb.css` is plain
CSS: no framework, no colours of its own. Borders and surfaces are
translucent greys and secondary text is dimmed, so the output follows
the font, text colour and background of the site, in light and dark
designs alike. The layout is responsive (card grid for the latest posts
and the related posts, image beside the text in the list view).

The layout :file:`Layouts/Default.html` adds the files listed in
:typoscript:`plugin.tx_golb.settings.css` with :html:`<f:asset.css>` as
long as :typoscript:`settings.includeCss` is enabled. Three ways to adapt
the look:

#.  **Tweak:** Override the custom properties on ``.golb`` in your own
    stylesheet, for example the spacing, the border radius or the width of
    the border and surface tints:

    ..  code-block:: css

        .golb {
            --golb-radius: 0;
            --golb-border: rgb(0 0 0 / 20%);
        }

#.  **Extend or replace:** Add your file to the list or remove the default:

    ..  code-block:: typoscript

        plugin.tx_golb.settings.css {
            golb >
            site = EXT:site_package/Resources/Public/Css/blog.css
        }

#.  **Switch off:** Set the constant
    :typoscript:`plugin.tx_golb.settings.includeCss = 0` and style the
    markup in the stylesheet of the site, or override the templates.
