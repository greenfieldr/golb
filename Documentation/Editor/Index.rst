..  include:: /Includes.rst.txt

..  _editor:

===================
Editing blog posts
===================

..  _editor-post:

Creating a blog post
====================

#.  Create a new page below the blog root page. In the page tree the
    "Blog post" page type is offered in the drag area of new pages, or
    change the :guilabel:`Type` of an existing page to :guilabel:`Blog
    post`.

#.  Fill in the page properties. In addition to the regular fields the
    :guilabel:`Blog` tab offers:

    ..  list-table::
        :header-rows: 1

        *   -   Field
            -   Meaning
        *   -   Publish date
            -   Date shown as the post date and used for sorting. If
                empty, the creation date of the page is used.
        *   -   Archived
            -   Marks the post as archived. Plugins can show all posts,
                only archived posts or only non-archived posts.
        *   -   Top post
            -   Marks the post as a top post (featured). Plugins can
                filter by this flag.
        *   -   Tags
            -   Tag records assigned to the post, see :ref:`editor-tags`.
        *   -   Related posts
            -   Hand-picked related posts. Related posts can also be found
                automatically by shared tags, see :ref:`templating-related`.

    The :guilabel:`Metadata` tab contains the standard fields
    :guilabel:`Author` and :guilabel:`Email` and the additional
    :guilabel:`Author image`.

#.  Assign :guilabel:`Categories` on the :guilabel:`Categories` tab. The
    plugins can filter by categories, sub categories are included.

#.  Add content elements to the page as usual. The post is rendered by
    the page template of your site.

..  tip::

    Posts can be organised in sub pages or folders below the blog root
    page, for example one folder per year. Hidden pages and pages below
    hidden pages are not listed.

..  _editor-tags:

Tags
====

Tags are records of the type :guilabel:`Tag` and can be created in any
page or folder with the :guilabel:`Web > List` module. A tag has a title
(unique per folder), an optional description and a slug for speaking
URLs. The :guilabel:`Blog posts` field on a tag shows and allows editing
the assigned posts.

..  _editor-plugins:

Plugins
=======

Both plugins are available in the :guilabel:`Golb` tab of the new content
element wizard.

List view
    Lists all matching posts with pagination.

Latest view
    Lists a limited number of posts, for example the three latest posts in
    a sidebar or on the start page.

On the :guilabel:`General` tab select the blog root page(s) in the field
:guilabel:`Pages`. All posts below these pages are collected
recursively. On the :guilabel:`Categories` tab select categories to
restrict the list. The :guilabel:`Plugin` tab contains the settings:

..  list-table::
    :header-rows: 1

    *   -   Setting
        -   Description
    *   -   Sorting field
        -   Publish date (with fallback to the creation date), creation
            date, last update, title or author. Default: publish date.
    *   -   Sorting direction
        -   Ascending or descending. Default: descending.
    *   -   Limit (Latest view)
        -   Maximum number of posts. ``0`` lists all posts.
    *   -   Items per page (List view)
        -   Number of posts per page of the pagination.
    *   -   Offset
        -   Number of posts to skip.
    *   -   Excluded blog posts
        -   Posts that must not be listed, for example the current post.
    *   -   Archived
        -   Show all posts, archived posts only or non-archived posts
            only.
    *   -   Top posts
        -   Show all posts, top posts only or non-top posts only.
    *   -   Allow demand overwrite
        -   Allows visitors to change the listing through URL parameters,
            for example a tag or category filter. See
            :ref:`configuration-demand`.
