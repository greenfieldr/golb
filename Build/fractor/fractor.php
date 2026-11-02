<?php

declare(strict_types=1);

/*
 * This file is part of the TYPO3 extension "golb".
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 3
 * of the License, or any later version.
 */

use a9f\Fractor\Configuration\FractorConfiguration;
use a9f\Typo3Fractor\Set\Typo3LevelSetList;

$extensionRoot = dirname(__DIR__, 2);

return FractorConfiguration::configure()
    ->withPaths([
        $extensionRoot . '/Configuration',
        $extensionRoot . '/Resources',
    ])
    ->withSets([
        Typo3LevelSetList::UP_TO_TYPO3_14,
    ]);
