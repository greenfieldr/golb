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

use Greenfieldr\Golb\Constants;
use Greenfieldr\Golb\Domain\Model\Category;
use Greenfieldr\Golb\Domain\Model\Page;
use Greenfieldr\Golb\Domain\Model\Tag;
use Greenfieldr\Golb\Domain\Repository\PageRepository;
use TYPO3\CMS\Core\MetaTag\MetaTagManagerRegistry;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\ContentObject\DataProcessorInterface;

/**
 * Provides the blog post (page model) of the currently rendered page to
 * Fluid, e.g. in a page template:
 *
 *   dataProcessing.10 = Greenfieldr\Golb\DataProcessing\MetaInformationProcessor
 *   dataProcessing.10.as = blogPost
 *   dataProcessing.10.metaTags = 1
 *
 * With "metaTags" enabled the processor adds the Open Graph type "article"
 * and the article:* meta tags (published and modified time, author,
 * section, tags) for blog post pages to the page header. Properties the
 * site has already set are left untouched.
 */
class MetaInformationProcessor implements DataProcessorInterface
{
    public function __construct(
        protected readonly PageRepository $pageRepository,
        protected readonly MetaTagManagerRegistry $metaTagManagerRegistry,
    ) {}

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
        $post = $this->pageRepository->findByIdentifier((int)($cObj->data['uid'] ?? 0));
        $processedData[$targetVariableName] = $post;

        if ($post instanceof Page
            && $post->getDoktype() === Constants::BLOG_POST_DOKTYPE
            && (bool)$cObj->stdWrapValue('metaTags', $processorConfiguration, false)
        ) {
            $this->addMetaTags($post);
        }

        return $processedData;
    }

    protected function addMetaTags(Page $post): void
    {
        $this->addMetaTag('og:type', 'article');

        $published = $post->getEffectiveDate();
        if ($published > 0) {
            $this->addMetaTag('article:published_time', date('c', $published));
        }
        $modified = $post->getTstamp() ?? 0;
        if ($modified > 0) {
            $this->addMetaTag('article:modified_time', date('c', $modified));
        }
        if ($post->getAuthor() !== '') {
            $this->addMetaTag('article:author', $post->getAuthor());
        }
        /** @var Category $category */
        foreach ($post->getCategories() as $category) {
            $this->addMetaTag('article:section', $category->getTitle());
            break;
        }
        /** @var Tag $tag */
        foreach ($post->getTags() as $tag) {
            $this->addMetaTag('article:tag', $tag->getTitle(), true);
        }
    }

    /**
     * Adds a meta tag with a "property" attribute unless the property was
     * already set, e.g. by the site or an SEO extension.
     */
    protected function addMetaTag(string $property, string $content, bool $multiple = false): void
    {
        $manager = $this->metaTagManagerRegistry->getManagerForProperty($property);
        if (!$multiple && $manager->getProperty($property, 'property') !== []) {
            return;
        }
        $manager->addProperty($property, $content, [], false, 'property');
    }
}
