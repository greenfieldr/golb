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

class MetaInformationProcessor implements DataProcessorInterface
{
    /**
     * @var PageRepository
     */
    protected $pageRepository;

    /**
     * BlogPostDataProcessor constructor.
     * @param PageRepository $pageRepository
     */
    public function __construct(PageRepository $pageRepository)
    {
        $this->pageRepository = $pageRepository;
    }

    /**
     * Returns a Golb page object of the currently displayed blog post
     *
     * @param ContentObjectRenderer $cObj The data of the content element or page
     * @param array $contentObjectConfiguration The configuration of Content Object
     * @param array $processorConfiguration The configuration of this processor
     * @param array $processedData Key/value store of processed data (e.g. to be passed to a Fluid View)
     * @return array the processed data as key/value store
     */
    public function process(
        ContentObjectRenderer $cObj,
        array $contentObjectConfiguration,
        array $processorConfiguration,
        array $processedData
    ) {
        $targetVariableName = $cObj->stdWrapValue('as', $processorConfiguration, 'blogPost');
        $blogPost = $this->pageRepository->findByIdentifier((int)$cObj->data['uid']);
        $processedData[$targetVariableName] = $blogPost;

        return $processedData;
    }
}
