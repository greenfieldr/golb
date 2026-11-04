<?php

/*
 * This file is part of the TYPO3 extension "golb".
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 3
 * of the License, or any later version.
 */

$EM_CONF[$_EXTKEY] = [
    'title' => 'Golb',
    'description' => 'Golb - Blog The Page Way! A TYPO3 blog extension that uses regular pages as blog posts.',
    'category' => 'fe',
    'author' => 'Marcel Küpper',
    'author_email' => 'typo3dev@kuepper.net',
    'state' => 'stable',
    'version' => '13.0.0',
    'constraints' => [
        'depends' => [
            'typo3' => '13.4.0-13.4.99',
            'php' => '8.2.0-8.4.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
