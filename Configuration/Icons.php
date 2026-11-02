<?php

declare(strict_types=1);

use TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider;

return [
    'apps-pagetree-post' => [
        'provider' => SvgIconProvider::class,
        'source' => 'EXT:golb/Resources/Public/Icons/apps-pagetree-post.svg'
    ],
    'apps-pagetree-post-hideinmenu' => [
        'provider' => SvgIconProvider::class,
        'source' => 'EXT:golb/Resources/Public/Icons/apps-pagetree-post-hideinmenu.svg'
    ],
    'ext-golb-tag' => [
        'provider' => SvgIconProvider::class,
        'source' => 'EXT:golb/Resources/Public/Icons/tx_golb_domain_model_tag.svg'
    ],
    'ext-golb-plugin-list-view' => [
        'provider' => SvgIconProvider::class,
        'source' => 'EXT:golb/Resources/Public/Icons/plugin_list_view.svg'
    ],
    'ext-golb-plugin-latest-view' => [
        'provider' => SvgIconProvider::class,
        'source' => 'EXT:golb/Resources/Public/Icons/plugin_latest_view.svg'
    ]
];
