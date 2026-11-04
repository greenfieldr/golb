<?php

/*
 * This file is part of the TYPO3 extension "golb".
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 3
 * of the License, or any later version.
 */

use Greenfieldr\Golb\Constants;
use TYPO3\CMS\Core\Domain\Repository\PageRepository;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

defined('TYPO3') || die();

$boot = function () {
    $ll = 'LLL:EXT:golb/Resources/Private/Language/locallang_db.xlf:';
    ExtensionManagementUtility::addTcaSelectItem(
        'pages',
        'doktype',
        [
            'label' => 'LLL:EXT:golb/Resources/Private/Language/locallang_be.xlf:doktype.post',
            'value' => (string)Constants::BLOG_POST_DOKTYPE,
            'icon' => 'apps-pagetree-post',
        ],
        '1',
        'after'
    );

    ArrayUtility::mergeRecursiveWithOverrule(
        $GLOBALS['TCA']['pages'],
        [
            'ctrl' => [
                'typeicon_classes' => [
                    (string)Constants::BLOG_POST_DOKTYPE => 'apps-pagetree-post',
                    (string)Constants::BLOG_POST_DOKTYPE . '-hideinmenu' => 'apps-pagetree-post',
                ],
            ],
            'types' => [
                (string)Constants::BLOG_POST_DOKTYPE
                    => $GLOBALS['TCA']['pages']['types'][PageRepository::DOKTYPE_DEFAULT],
            ],
        ]
    );

    $tempColumns = [
        // System fields are not part of the TCA columns of "pages". They are
        // exposed as passthrough fields so Extbase can map them onto the Page
        // model (creation date fallback for sorting, modification date, sorting).
        'crdate' => [
            'label' => 'crdate',
            'config' => [
                'type' => 'passthrough',
            ],
        ],
        'tstamp' => [
            'label' => 'tstamp',
            'config' => [
                'type' => 'passthrough',
            ],
        ],
        'sorting' => [
            'label' => 'sorting',
            'config' => [
                'type' => 'passthrough',
            ],
        ],
        'tx_golb_related' => [
            'exclude' => 0,
            'label' => $ll . 'pages.tx_golb_related',
            'displayCond' => 'FIELD:doktype:=:' . Constants::BLOG_POST_DOKTYPE,
            'config' => [
                'type' => 'select',
                'renderType' => 'selectMultipleSideBySide',
                'MM' => 'tx_golb_page_related_page_mm',
                'foreign_table' => 'pages',
                'foreign_table_where' => ' AND {#pages}.{#doktype} = ' . Constants::BLOG_POST_DOKTYPE . ' AND ({#pages}.{#sys_language_uid} IN (-1,0) OR {#pages}.{#l10n_parent} = 0) ORDER BY pages.title',
                'size' => 5,
                'minitems' => 0,
                'maxitems' => 99,
                'multiple' => false,

                'behaviour' => [
                    'allowLanguageSynchronization' => true,
                ],
            ],
        ],
        'tx_golb_content_elements' => [
            'exclude' => 0,
            'label' => $ll . 'pages.tx_golb_contentElements',
            'displayCond' => 'FIELD:doktype:=:' . Constants::BLOG_POST_DOKTYPE,
            'config' => [
                'type' => 'inline',
                'foreign_table' => 'tt_content',
                'foreign_field' => 'pid',
                'foreign_sortby' => 'sorting',
                'maxitems' => 9999,
            ],
        ],
        'tx_golb_author_image' => [
            'exclude' => 0,
            'label' => $ll . 'pages.tx_golb_author_image',
            'displayCond' => 'FIELD:doktype:=:' . Constants::BLOG_POST_DOKTYPE,
            'config' => [
                'type' => 'file',
                'appearance' => [
                    'createNewRelationLinkTitle' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:images.addFileReference',
                ],
                'minitems' => 0,
                'maxitems' => 1,
            ],
        ],
        'tx_golb_subpages' => [
            'exclude' => 0,
            'label' => $ll . 'pages.tx_golb_subpages',
            'displayCond' => 'FIELD:doktype:=:' . Constants::BLOG_POST_DOKTYPE,
            'config' => [
                'type' => 'inline',
                'foreign_table' => 'pages',
                'foreign_field' => 'pid',
                'maxitems' => 9999,
            ],
        ],
        'tx_golb_publish_date' => [
            'label' => $ll . 'pages.tx_golb_publish_date',
            'displayCond' => 'FIELD:doktype:=:' . Constants::BLOG_POST_DOKTYPE,
            'config' => [
                'type' => 'datetime',
                'size' => '13',
                'default' => 0,
            ],
        ],
        'tx_golb_tags' => [
            'exclude' => true,
            'label' => $ll . 'pages.tx_golb_tags',
            'displayCond' => 'FIELD:doktype:=:' . Constants::BLOG_POST_DOKTYPE,
            'config' => [
                'type' => 'select',
                'renderType' => 'selectMultipleSideBySide',
                'MM' => 'tx_golb_page_tag_mm',
                'foreign_table' => 'tx_golb_domain_model_tag',
                'foreign_table_where' => ' AND {#tx_golb_domain_model_tag}.{#sys_language_uid} IN (-1,0) ORDER BY tx_golb_domain_model_tag.title',
                'size' => 10,
                'minitems' => 0,
                'maxitems' => 99,
                'multiple' => false,
            ],
        ],
        'tx_golb_archived' => [
            'exclude' => true,
            'label' => $ll . 'pages.tx_golb_archived',
            'displayCond' => 'FIELD:doktype:=:' . Constants::BLOG_POST_DOKTYPE,
            'config' => [
                'type' => 'check',
                'default' => 0,
            ],
        ],
        'tx_golb_top_post' => [
            'exclude' => true,
            'label' => $ll . 'pages.tx_golb_top_post',
            'displayCond' => 'FIELD:doktype:=:' . Constants::BLOG_POST_DOKTYPE,
            'config' => [
                'type' => 'check',
                'default' => 0,
            ],
        ],
    ];

    ExtensionManagementUtility::addTCAcolumns('pages', $tempColumns);
    ExtensionManagementUtility::addToAllTCAtypes(
        'pages',
        '--div--;LLL:EXT:golb/Resources/Private/Language/locallang_db.xlf:pages.golbTab, '
            . 'tx_golb_publish_date, tx_golb_archived, tx_golb_top_post, tx_golb_tags, tx_golb_related'
    );
    ExtensionManagementUtility::addToAllTCAtypes(
        'pages',
        'tx_golb_author_image',
        '',
        'after:author_email'
    );
};
$boot();
unset($boot);
