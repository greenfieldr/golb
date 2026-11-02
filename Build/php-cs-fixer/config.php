<?php

declare(strict_types=1);

use TYPO3\CodingStandards\CsFixerConfig;

$config = CsFixerConfig::create();
$config->setHeader(
    'This file is part of the TYPO3 extension "golb".' . PHP_EOL . PHP_EOL
    . 'It is free software; you can redistribute it and/or modify it under' . PHP_EOL
    . 'the terms of the GNU General Public License, either version 3' . PHP_EOL
    . 'of the License, or any later version.',
    true
);
$config->setCacheFile(dirname(__DIR__, 2) . '/.Build/.cache/php-cs-fixer.cache');
$config->getFinder()
    ->in(dirname(__DIR__, 2))
    ->exclude(['.Build', 'Documentation', 'Documentation-GENERATED-temp'])
    ->name('*.php')
    ->ignoreDotFiles(true);

return $config;
