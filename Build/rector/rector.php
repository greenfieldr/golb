<?php

declare(strict_types=1);

/*
 * This file is part of the TYPO3 extension "golb".
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 3
 * of the License, or any later version.
 */

use Rector\Config\RectorConfig;
use Rector\Set\ValueObject\LevelSetList;
use Rector\Set\ValueObject\SetList;
use Rector\ValueObject\PhpVersion;
use Ssch\TYPO3Rector\CodeQuality\General\ConvertImplicitVariablesToExplicitGlobalsRector;
use Ssch\TYPO3Rector\Configuration\Typo3Option;
use Ssch\TYPO3Rector\Set\Typo3LevelSetList;
use Ssch\TYPO3Rector\Set\Typo3SetList;
use Ssch\TYPO3Rector\TYPO311\v5\MigrateExtbaseViewInterfaceRector;

$extensionRoot = dirname(__DIR__, 2);

return RectorConfig::configure()
    ->withPaths([
        $extensionRoot . '/Classes',
        $extensionRoot . '/Configuration',
        $extensionRoot . '/Tests',
        $extensionRoot . '/ext_localconf.php',
    ])
    ->withPhpVersion(PhpVersion::PHP_82)
    ->withSets([
        SetList::CODE_QUALITY,
        LevelSetList::UP_TO_PHP_82,
        Typo3SetList::CODE_QUALITY,
        Typo3SetList::GENERAL,
        Typo3LevelSetList::UP_TO_TYPO3_13,
    ])
    ->withPHPStanConfigs([
        Typo3Option::PHPSTAN_FOR_RECTOR_PATH,
    ])
    ->withImportNames(importShortClasses: false, removeUnusedImports: true)
    ->withCache($extensionRoot . '/.Build/.cache/rector')
    ->withSkip([
        ConvertImplicitVariablesToExplicitGlobalsRector::class,
        // initializeView() is typed against the core ViewInterface (TYPO3 13+)
        MigrateExtbaseViewInterfaceRector::class,
        $extensionRoot . '/.Build/*',
        $extensionRoot . '/Build/*',
    ]);
