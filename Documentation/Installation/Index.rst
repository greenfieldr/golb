..  include:: /Includes.rst.txt

..  _installation:

============
Installation
============

..  _installation-composer:

Composer installation
=====================

Require the extension in your project:

..  code-block:: bash

    composer require greenfieldr/golb

The extension key is ``golb``. TYPO3 picks the package up automatically.

..  _installation-ter:

Installation via Extension Manager
==================================

Download the extension from the
`TYPO3 Extension Repository <https://extensions.typo3.org/extension/golb>`__
and activate it in the :guilabel:`Admin Tools > Extensions` module.

..  _installation-setup:

Setup
=====

#.  **Database:** Run :guilabel:`Admin Tools > Maintenance > Analyze
    Database Structure` to create the additional fields and the tag
    tables. The database schema is derived from the TCA, no
    :file:`ext_tables.sql` is shipped.

#.  **TypoScript:** Include the static TypoScript "Golb" in your site
    package (:guilabel:`Web > Template > Includes`) or import it in your
    TypoScript setup:

    ..  code-block:: typoscript

        @import 'EXT:golb/Configuration/TypoScript/setup.typoscript'

#.  **Blog root page:** Create a page (any type) that will contain the
    posts, for example "Blog". Posts can be nested in sub pages or folders
    below it; the plugins collect them recursively.

#.  **Plugins:** Add the content element :guilabel:`Golb > List view` or
    :guilabel:`Golb > Latest view` to a page and select the blog root
    page(s) in the :guilabel:`Pages` field. See :ref:`editor-plugins`.

#.  **Frontend:** The plugins come styled: the default templates add a
    plain stylesheet to the page. Disable it with the constant
    :typoscript:`plugin.tx_golb.settings.includeCss = 0` if you want to
    style the markup yourself, and insert :typoscript:`lib.golb.post` in
    the page template to render post pages, see :ref:`templating`.

#.  **Optional:** Decide where tags are stored. Tag records can be created
    in any page or folder; a dedicated "Tags" folder below the blog root
    is a common choice.

..  _installation-upgrade-wizard:

Upgrade wizard
==============

When upgrading from a release of the 3.x series run the upgrade wizard
:guilabel:`Migrates pages field for related posts` in
:guilabel:`Admin Tools > Upgrade > Upgrade Wizard`. It fixes ``NULL``
values in the field :sql:`pages.tx_golb_related` left behind by an
older schema. See :ref:`upgrade` for other changes.
