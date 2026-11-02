<?php

declare(strict_types=1);

/*
 * This file is part of the TYPO3 extension "golb".
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 3
 * of the License, or any later version.
 */

namespace Greenfieldr\Golb\DataProcessing;

use Greenfieldr\Golb\Domain\Repository\PageRepository;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\ContentObject\DataProcessorInterface;

/**
 * Provides the blog post (page model) of the currently rendered page to
 * Fluid, e.g. in a page template:
 *
 *   dataProcessing.10 = Greenfieldr\Golb\DataProcessing\MetaInformationProcessor
 *   dataProcessing.10.as = blogPost
 */
class MetaInformationProcessor implements DataProcessorInterface
{
    public function __construct(protected readonly PageRepository $pageRepository) {}

    /**
     * @param ContentObjectRenderer $cObj The data of the content element or page
     * @param array<mixed> $contentObjectConfiguration The configuration of Content Object
     * @param array<mixed> $processorConfiguration The configuration of this processor
     * @param array<mixed> $processedData Key/value store of processed data (e.g. to be passed to a Fluid View)
     * @return array<mixed> the processed data as key/value store
     */
    public function process(
        ContentObjectRenderer $cObj,
        array $contentObjectConfiguration,
        array $processorConfiguration,
        array $processedData
    ): array {
        $targetVariableName = (string)$cObj->stdWrapValue('as', $processorConfiguration, 'blogPost');
        $processedData[$targetVariableName] = $this->pageRepository->findByIdentifier((int)($cObj->data['uid'] ?? 0));

        return $processedData;
    }
}
