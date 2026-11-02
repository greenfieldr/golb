<?php

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
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * This view helper returns related posts based on tags
 */
class RelatedPostsViewHelper extends AbstractViewHelper
{
    /**
     * @var bool
     */
    protected $escapeChildren = false;
    /**
     * Disable the output escaping interceptor so that the value is not htmlspecialchar'd twice
     *
     * @var bool
     */
    protected $escapeOutput = false;

    /**
     * Constructor.
     *
     * @param PageRepository $pageRepository
     */
    public function __construct(
        /**
         * Injects PageRepository
         */
        protected PageRepository $pageRepository
    ) {}

    public function initializeArguments(): void
    {
        $this->registerArgument('posts', 'array', 'The list of root pages to search in', true);
        $this->registerArgument('currentPost', Page::class, 'The currently displayed blog post.', true);
        $this->registerArgument('limit', 'int', 'The number of related posts to return', false, 3);
        $this->registerArgument('as', 'string', 'The variable name', false, 'relatedPosts');
    }

    /**
     * This view helper returns related posts based on tags.
     *
     * @return string
     */
    public function render(): string
    {
        $tagArray = [];
        /** @var Tag $tag */
        foreach ($this->arguments['currentPost']->getTags() as $tag) {
            $tagArray[] = $tag->getTitle();
        }
        $tags = $tagArray;

        $posts = $this->pageRepository->findByTags(
            $this->arguments['posts'],
            $tags,
            [$this->arguments['currentPost']->getUid()],
            $this->arguments['limit']
        );

        $this->templateVariableContainer->add($this->arguments['as'], $posts);
        $content = $this->renderChildren();
        $this->templateVariableContainer->remove($this->arguments['as']);
        return $content;
    }
}
