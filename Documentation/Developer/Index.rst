..  include:: /Includes.rst.txt

..  _developer:

================
Developer corner
================

..  _developer-demand:

The demand object
=================

:php:`\Greenfieldr\Golb\Domain\Model\Dto\PostsDemand` describes a listing:
which posts, in which order, how many. The controller builds it from the
plugin settings and, if allowed, from the request arguments. A demand can
also be built in PHP:

..  code-block:: php

    use Greenfieldr\Golb\Domain\Model\Dto\PostsDemand;

    $demand = (new PostsDemand())
        ->setOrder(PostsDemand::ORDER_DATE)
        ->setOrderDirection('DESC')
        ->setNonArchived(true)
        ->setTags([3, 4])
        ->setLimit(5);

Every setting has a "has" or "isSet" method, so defaults only apply when
nothing was set. Available settings: ``categories`` and ``category``
(sub categories are included), ``tags`` and ``tag``, ``excluded``,
``archived``, ``nonArchived``, ``topPost``, ``nonTopPost``, ``order``,
``orderDirection``, ``limit``, ``offset``, ``itemsPerPage`` and
``currentPage``.

..  _developer-repositories:

Repositories
============

:php:`\Greenfieldr\Golb\Domain\Repository\PageRepository`
    ``findPosts(array $rootPages, PostsDemand $demand): Page[]`` returns
    the posts below the root pages according to the demand.
    ``findByTags(array $rootPages, array $tagTitles, array $exclude, int $limit)``
    returns related posts ranked by the number of shared tags.
    ``findSubPagesByPageIds(array $rootPages): int[]`` returns the uids of
    all visible pages below the root pages.

:php:`\Greenfieldr\Golb\Domain\Repository\TagRepository`
    ``findAll()`` and ``findAllWithAtLeastOneBlogPost()`` for tag clouds
    or filter navigations.

:php:`\Greenfieldr\Golb\Domain\Repository\CategoryRepository`
    ``findByRelation(int $uid, string $table = 'tt_content', string $field = 'categories')``
    returns the categories assigned to a record.

The repositories ignore the storage page setting; the root pages of the
listing define the scope.

..  _developer-how-it-works:

How posts are collected
=======================

#.  The uids of all pages below the root pages are collected level by
    level with the QueryBuilder. Hidden, deleted and time restricted pages
    are skipped together with their sub pages. In a translated frontend
    the uids of the translated pages are used.
#.  Pages of the blog post doktype are loaded through Extbase, so enable
    fields, language overlays and lazy relations are handled by Extbase.
#.  Posts are sorted and filtered in PHP: by excluded uids, archived and
    top post flags, categories (including sub categories) and tags.
#.  Offset and limit are applied to the result. The list view sets the
    limit to ``0`` and paginates the full result with an
    :php:`ArrayPaginator`.

This keeps the extension independent of SQL dialects and handles the
page tree, but it loads all posts of the root pages. For blogs with many
thousands of posts consider caching the plugin output.

..  _developer-extending:

Extending
=========

*   **Own controller actions or plugins:** Extend
    :php:`\Greenfieldr\Golb\Controller\BlogController` or register your own
    plugin with the repositories; all services are autowired.
*   **Additional page fields:** Add columns to the ``pages`` TCA, extend
    the :php:`Page` model (or XCLASS it) and map the property in
    :file:`Configuration/Extbase/Persistence/Classes.php`.
*   **Tag listings:** Inject the :php:`TagRepository` into your own
    ViewHelper or data processor and link tags with the argument
    ``demand[tag]`` of the list plugin.

..  _developer-qa:

Development
===========

Clone the repository and install the development dependencies:

..  code-block:: bash

    composer install
    composer ci          # coding standards, PHPStan, Rector, Fractor, tests
    composer fix         # apply Rector, Fractor and coding standard fixes

The functional tests run against SQLite by default
(``typo3DatabaseDriver=pdo_sqlite``). The documentation can be rendered
locally with the official Docker image:

..  code-block:: bash

    docker run --rm -it -v "$(pwd)":/project \
        ghcr.io/typo3-documentation/render-guides:latest --config=Documentation
