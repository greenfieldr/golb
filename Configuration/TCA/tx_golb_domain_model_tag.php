<?php
declare(strict_types=1);

use Greenfieldr\Golb\Constants;

defined('TYPO3') || die();

$ll = 'LLL:EXT:golb/Resources/Private/Language/locallang_db.xlf:';

return [
    'ctrl' => [
        'title' => $ll . 'tx_golb_domain_model_tag',
        'label' => 'title',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'languageField' => 'sys_language_uid',
        'transOrigPointerField' => 'l10n_parent',
        'transOrigDiffSourceField' => 'l10n_diffsource',
        'translationSource' => 'l10n_source',
        'versioningWS' => true,
        'origUid' => 't3_origuid',
        'default_sortby' => 'ORDER BY sorting',
        'sortby' => 'sorting',
        'delete' => 'deleted',
        'searchFields' => 'title',
        'editlock' => 'editlock',
        'enablecolumns' => [
            'disabled' => 'hidden',
            'starttime' => 'starttime',
            'endtime' => 'endtime',
            'fe_group' => 'fe_group'
        ],
        'typeicon_classes' => [
            'default' => 'ext-golb-tag'
        ],
        'security' => [
            'ignorePageTypeRestriction' => true,
        ]
    ],
    'columns' => [
        'pid' => [
            'label' => 'pid',
            'config' => [
                'type' => 'passthrough'
            ]
        ],
        'crdate' => [
            'label' => 'crdate',
            'config' => [
                'type' => 'datetime',
            ]
        ],
        'tstamp' => [
            'label' => 'tstamp',
            'config' => [
                'type' => 'datetime',
            ]
        ],
        'sys_language_uid' => [
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.language',
            'config' => ['type' => 'language']
        ],
        'l10n_parent' => [
            'displayCond' => 'FIELD:sys_language_uid:>:0',
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.l18n_parent',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['label' => '', 'value' => 0],
                ],
                'foreign_table' => 'tx_golb_domain_model_tag',
                'foreign_table_where' => 'AND {#tx_golb_domain_model_tag}.{#pid}=###CURRENT_PID### AND {#tx_golb_domain_model_tag}.{#sys_language_uid} IN (-1,0)',
                'default' => 0,
            ]
        ],
        'l10n_source' => [
            'config' => [
                'type' => 'passthrough'
            ]
        ],
        'l10n_diffsource' => [
            'config' => [
                'type' => 'passthrough',
                'default' => ''
            ]
        ],
        'hidden' => [
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.hidden',
            'config' => [
                'type' => 'check',
                'default' => 0
            ]
        ],
        'title' => [
            'exclude' => false,
            'label' => $ll . 'tx_golb_domain_model_tag.title',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'behaviour' => [
                    'allowLanguageSynchronization' => true,
                ],
                'eval' => 'uniqueInPid',
            ]
        ],
        'description' => [
            'exclude' => true,
            'label' => $ll . 'tx_golb_domain_model_tag.description',
            'config' => [
                'type' => 'text',
                'cols' => 30,
                'rows' => 5,
                'behaviour' => [
                    'allowLanguageSynchronization' => true,
                ],
            ]
        ],
        'slug' => [
            'exclude' => false,
            'label' => $ll . 'tx_golb_domain_model_tag.slug',
            'config' => [
                'type' => 'slug',
                'size' => 50,
                'generatorOptions' => [
                    'fields' => [
                        'title',
                    ],
                    'fieldSeparator' => '/',
                    'prefixParentPageSlug' => true,
                ],
                'fallbackCharacter' => '-',
                'eval' => 'uniqueInSite',
                'default' => '',
            ]
        ],
        'pages' => [
            'exclude' => true,
            'label' => $ll . 'tx_golb_domain_model_tag.pages',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectMultipleSideBySide',
                'MM' => 'tx_golb_page_tag_mm',
                'MM_opposite_field' => 'tx_golb_tags',
                'MM_oppositeUsage' => [],
                'foreign_table' => 'pages',
                'foreign_table_where' => ' AND {#pages}.{#doktype} = ' . Constants::BLOG_POST_DOKTYPE . ' ORDER BY pages.title',
                'size' => 10,
                'minitems' => 0,
                'maxitems' => 99,
                'multiple' => false,
            ],
        ],
    ],
    'types' => [
        0 => [
            'showitem' => 'hidden, title, description, slug, pages,'
        ]
    ]
];
