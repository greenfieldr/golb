<?php

/*
 * This file is part of the TYPO3 extension "golb".
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 3
 * of the License, or any later version.
 */

use Greenfieldr\Golb\Constants;
use TYPO3\CMS\Core\DataHandling\PageDoktypeRegistry;
use TYPO3\CMS\Core\Utility\GeneralUtility;

defined('TYPO3') || die();

$boot = function ($packageKey) {
    GeneralUtility::makeInstance(PageDoktypeRegistry::class)->add(Constants::BLOG_POST_DOKTYPE, [
        'type' => 'web',
        'allowedTables' => '*',
    ]);
};

$boot('golb');
unset($boot);
