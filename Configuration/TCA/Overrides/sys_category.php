<?php

/*
 * This file is part of the TYPO3 extension "golb".
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 3
 * of the License, or any later version.
 */

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

defined('TYPO3') || die();

$boot = function () {
    $tempColumns = [
        'tx_golb_sub_categories' => [
            'exclude' => 0,
            'label' => 'LLL:EXT:golb/Resources/Private/Language/locallang_db.xlf:sys_category.tx_golb_sub_categories',
            'config' => [
                'type' => 'inline',
                'foreign_table' => 'sys_category',
                'foreign_field' => 'parent',
                'maxitems' => 9999,
            ],
        ],
    ];

    ExtensionManagementUtility::addTCAcolumns('sys_category', $tempColumns);
    ExtensionManagementUtility::addToAllTCAtypes(
        'sys_category',
        'tx_golb_sub_categories'
    );

};
$boot();
unset($boot);
