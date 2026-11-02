<?php

declare(strict_types=1);

/*
 * This file is part of the TYPO3 extension "golb".
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 3
 * of the License, or any later version.
 */

namespace Greenfieldr\Golb\ViewHelpers;

use Greenfieldr\Golb\Domain\Model\Page;
use Greenfieldr\Golb\Domain\Model\Tag;
use Greenfieldr\Golb\Domain\Repository\PageRepository;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Provides the posts related to the current post (by shared tags) to the
 * child content:
 *
 *   <golb:relatedPosts posts="{0: 12}" currentPost="{blogPost}" limit="3" as="relatedPosts">
 *       <f:for each="{relatedPosts}" as="related">{related.post.title} ({related.amount})</f:for>
 *   </golb:relatedPosts>
 *
 * Import the namespace with xmlns:golb="http://typo3.org/ns/Greenfieldr/Golb/ViewHelpers".
 */
final class RelatedPostsViewHelper extends AbstractViewHelper
{
    /**
     * @var bool|null
     */
    protected $escapeChildren = false;

    /**
     * @var bool
     */
    protected $escapeOutput = false;

    public function __construct(private readonly PageRepository $pageRepository) {}

    public function initializeArguments(): void
    {
        $this->registerArgument('posts', 'array', 'The root page uids to search in', true);
        $this->registerArgument('currentPost', Page::class, 'The currently displayed blog post', true);
        $this->registerArgument('limit', 'int', 'The number of related posts to return', false, 3);
        $this->registerArgument('as', 'string', 'The variable name', false, 'relatedPosts');
    }

    public function render(): string
    {
        $currentPost = $this->arguments['currentPost'];
        if (!$currentPost instanceof Page) {
            throw new \InvalidArgumentException('The argument "currentPost" must be a blog post page object.', 1762100000);
        }

        $rootPages = [];
        $posts = $this->arguments['posts'];
        foreach (is_array($posts) ? $posts : [] as $rootPage) {
            if (is_int($rootPage) || (is_string($rootPage) && is_numeric($rootPage))) {
                $rootPages[] = (int)$rootPage;
            }
        }

        $limit = $this->arguments['limit'];
        $limit = is_int($limit) ? $limit : 3;

        $as = $this->arguments['as'];
        $as = is_string($as) && $as !== '' ? $as : 'relatedPosts';

        $tagTitles = [];
        /** @var Tag $tag */
        foreach ($currentPost->getTags() as $tag) {
            $tagTitles[] = $tag->getTitle();
        }

        $relatedPosts = $this->pageRepository->findByTags(
            $rootPages,
            $tagTitles,
            [(int)$currentPost->getUid()],
            $limit
        );

        $renderingContext = $this->renderingContext;
        if (!$renderingContext instanceof RenderingContextInterface) {
            throw new \RuntimeException('The ViewHelper has no rendering context.', 1762100001);
        }
        $variableProvider = $renderingContext->getVariableProvider();
        $variableProvider->add($as, $relatedPosts);
        $content = $this->renderChildren();
        $variableProvider->remove($as);

        return is_scalar($content) ? (string)$content : '';
    }
}
