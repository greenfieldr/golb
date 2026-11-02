<?php
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\DataHandling\PageDoktypeRegistry;
use Greenfieldr\Golb\Constants;

defined('TYPO3') || die();

$boot = function ($packageKey) {
    GeneralUtility::makeInstance(PageDoktypeRegistry::class)->add(Constants::BLOG_POST_DOKTYPE, [
        'type' => 'web',
        'allowedTables' => '*',
    ]);
};

$boot('golb');
unset($boot);
