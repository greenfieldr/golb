<?php

/*
 * This file is part of the TYPO3 extension "golb".
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 3
 * of the License, or any later version.
 */

use Greenfieldr\Golb\Constants;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

defined('TYPO3') || die();

$boot = function () {
    ExtensionManagementUtility::addStaticFile('golb', 'Configuration/TypoScript', 'Golb');

    ExtensionManagementUtility::addUserTSConfig(
        'options.pageTree.doktypesToShowInNewPageDragArea := addToList(' . Constants::BLOG_POST_DOKTYPE . ')'
    );
};
$boot();
unset($boot);
