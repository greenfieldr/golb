<?php

declare(strict_types=1);

/*
 * This file is part of the TYPO3 extension "golb".
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 3
 * of the License, or any later version.
 */

use Greenfieldr\Golb\Domain\Model\Category;
use Greenfieldr\Golb\Domain\Model\ContentElement;
use Greenfieldr\Golb\Domain\Model\Page;

return [
    Page::class => [
        'tableName' => 'pages',
        'properties' => [
            'creationDate' => [
                'fieldName' => 'crdate',
            ],
            'extendToSubpages' => [
                'fieldName' => 'extendToSubpages',
            ],
            'lastUpdated' => [
                'fieldName' => 'lastUpdated',
            ],
            'newUntil' => [
                'fieldName' => 'newUntil',
            ],
            'authorImage' => [
                'fieldName' => 'tx_golb_author_image',
            ],
            'relatedPages' => [
                'fieldName' => 'tx_golb_related',
            ],
            'contentElements' => [
                'fieldName' => 'tx_golb_content_elements',
            ],
            'subpages' => [
                'fieldName' => 'tx_golb_subpages',
            ],
            'publishDate' => [
                'fieldName' => 'tx_golb_publish_date',
            ],
            'tags' => [
                'fieldName' => 'tx_golb_tags',
            ],
            'archived' => [
                'fieldName' => 'tx_golb_archived',
            ],
            'topPost' => [
                'fieldName' => 'tx_golb_top_post',
            ],
        ],
    ],
    ContentElement::class => [
        'tableName' => 'tt_content',
    ],
    Category::class => [
        'tableName' => 'sys_category',
        'properties' => [
            'subCategories' => [
                'fieldName' => 'tx_golb_sub_categories',
            ],
        ],
    ],
];
