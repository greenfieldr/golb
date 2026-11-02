<?php

declare(strict_types=1);

use a9f\Fractor\Configuration\FractorConfiguration;
use a9f\Typo3Fractor\Set\Typo3LevelSetList;

$extensionRoot = dirname(__DIR__, 2);

return FractorConfiguration::configure()
    ->withPaths([
        $extensionRoot . '/Configuration',
        $extensionRoot . '/Resources',
    ])
    ->withSets([
        Typo3LevelSetList::UP_TO_TYPO3_13,
    ]);
