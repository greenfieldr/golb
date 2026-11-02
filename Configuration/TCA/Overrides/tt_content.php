<?php
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

defined('TYPO3') || die();

$boot = function () {
    $pluginSignatureList = ExtensionUtility::registerPlugin(
        'golb',
        'ListView',
        'LLL:EXT:golb/Resources/Private/Language/locallang_be.xlf:tt_content.plugin.listView.title',
        'ext-golb-plugin-list-view',
        'Golb'
    );
    ExtensionManagementUtility::addPiFlexFormValue(
        '*',
        'FILE:EXT:golb/Configuration/FlexForms/ListView.xml',
        $pluginSignatureList
    );

    $pluginSignatureLatest = ExtensionUtility::registerPlugin(
        'golb',
        'LatestView',
        'LLL:EXT:golb/Resources/Private/Language/locallang_be.xlf:tt_content.plugin.latestView.title',
        'ext-golb-plugin-latest-view',
        'Golb'
    );
    ExtensionManagementUtility::addPiFlexFormValue(
        '*',
        'FILE:EXT:golb/Configuration/FlexForms/LatestView.xml',
        $pluginSignatureLatest
    );

    $GLOBALS['TCA']['tt_content']['types'][$pluginSignatureLatest]['showitem'] =
    $GLOBALS['TCA']['tt_content']['types'][$pluginSignatureList]['showitem'] = '
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,
            --palette--;;general,
            --palette--;;headers,
            pages,
        --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.plugin,
            pi_flexform,
        --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.appearance,
            --palette--;;frames,
            --palette--;;appearanceLinks,
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:language,
            --palette--;;language,
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,
            --palette--;;hidden,
            --palette--;;access,
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:categories,
            categories,
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:notes,
            rowDescription,
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:extended,
    ';
};
$boot();
unset($boot);
