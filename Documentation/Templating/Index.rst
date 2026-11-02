..  include:: /Includes.rst.txt

..  _templating:

==========
Templating
==========

..  _templating-templates:

Templates
=========

The default templates are deliberately simple: an unordered list of posts
with title, date, author, abstract, categories and tags. Copy them to your
site package and register the path as described in
:ref:`configuration-typoscript`.

..  code-block:: text

    Resources/Private/
    ├── Layouts/Default.html         Wraps the output in <div class="golb">
    ├── Partials/Post.html           One post in a list
    ├── Partials/Pagination.html     Pagination of the list view
    └── Templates/Blog/
        ├── Latest.html              Latest view plugin
        └── List.html                List view plugin

Variables available in the templates:

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
        -   The demand object, e.g. ``demand.currentPage``
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
    *   -   ``media``
        -   File references of the page "Media" field
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

A blog post is rendered by the page template of the site. To access the
post object (for example to show the publish date, author or tags in the
page header), add the data processor to the page object:

..  code-block:: typoscript

    page.10 = PAGEVIEW
    page.10 {
        paths.10 = EXT:site_package/Resources/Private/Templates/
        dataProcessing {
            10 = Greenfieldr\Golb\DataProcessing\MetaInformationProcessor
            10.as = blogPost
        }
    }

``blogPost`` is then the :php:`Page` object of the current page (also for
pages that are not blog posts, check ``blogPost.doktype`` if necessary).

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

The ViewHelper :html:`<golb:relatedPosts>` finds posts that share tags
with the current post, best matches first. Import the namespace in the
template with ``xmlns:golb="http://typo3.org/ns/Greenfieldr/Golb/ViewHelpers"``
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
