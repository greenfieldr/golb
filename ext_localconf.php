<?php
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use Greenfieldr\Golb\Controller\BlogController;

defined('TYPO3') || die();

$boot = function ($packageKey) {
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['fluid']['namespaces']['golb'][] = 'Greenfieldr\\Golb\\ViewHelpers';

    ExtensionUtility::configurePlugin(
        GeneralUtility::underscoredToUpperCamelCase($packageKey),
        'ListView',
        [
            BlogController::class => 'list'
        ],
        [],
        ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT
    );
    ExtensionUtility::configurePlugin(
        GeneralUtility::underscoredToUpperCamelCase($packageKey),
        'LatestView',
        [
            BlogController::class => 'latest'
        ],
        [],
        ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT
    );
};

$boot('golb');
unset($boot);
