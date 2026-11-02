<?php

/*
 * This file is part of the TYPO3 extension "golb".
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 3
 * of the License, or any later version.
 */

use Greenfieldr\Golb\Controller\BlogController;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

defined('TYPO3') || die();

$boot = function ($packageKey) {
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['fluid']['namespaces']['golb'][] = 'Greenfieldr\\Golb\\ViewHelpers';

    ExtensionUtility::configurePlugin(
        GeneralUtility::underscoredToUpperCamelCase($packageKey),
        'ListView',
        [
            BlogController::class => 'list',
        ],
        []
    );
    ExtensionUtility::configurePlugin(
        GeneralUtility::underscoredToUpperCamelCase($packageKey),
        'LatestView',
        [
            BlogController::class => 'latest',
        ],
        []
    );
};

$boot('golb');
unset($boot);
