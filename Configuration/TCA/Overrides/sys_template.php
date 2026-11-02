<?php
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use Greenfieldr\Golb\Constants;

defined('TYPO3') || die();

$boot = function () {
    ExtensionManagementUtility::addStaticFile('golb', 'Configuration/TypoScript', 'Golb');


    ExtensionManagementUtility::addUserTSConfig(
        'options.pageTree.doktypesToShowInNewPageDragArea := addToList(' . Constants::BLOG_POST_DOKTYPE . ')'
    );
};
$boot();
unset($boot);
