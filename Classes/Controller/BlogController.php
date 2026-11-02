<?php

declare(strict_types=1);

/*
 * This file is part of the TYPO3 extension "golb".
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 3
 * of the License, or any later version.
 */

namespace Greenfieldr\Golb\Controller;

use Greenfieldr\Golb\Domain\Model\Category;
use Greenfieldr\Golb\Domain\Model\Dto\PostsDemand;
use Greenfieldr\Golb\Domain\Repository\CategoryRepository;
use Greenfieldr\Golb\Domain\Repository\PageRepository;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Core\Pagination\ArrayPaginator;
use TYPO3\CMS\Core\Pagination\SimplePagination;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\View\ViewInterface;

/**
 * Renders lists of blog posts for the "List view" and "Latest view" plugins.
 */
class BlogController extends BaseController
{
    /**
     * Root page uids (plugin field "pages") to collect blog posts from
     *
     * @var int[]
     */
    protected array $pages = [];

    /**
     * Categories selected in the plugin record to filter posts by
     *
     * @var Category[]
     */
    protected array $categories = [];

    public function __construct(
        protected readonly PageRepository $pageRepository,
        protected readonly CategoryRepository $categoryRepository
    ) {}

    public function initializeAction(): void
    {
        parent::initializeAction();

        $data = $this->getContentObjectData();
        $this->pages = GeneralUtility::intExplode(',', (string)($data['pages'] ?? ''), true);
        $this->categories = $this->categoryRepository->findByRelation((int)($data['uid'] ?? 0));

        if ($this->arguments->hasArgument('demand')) {
            $this->arguments->getArgument('demand')
                ->getPropertyMappingConfiguration()
                ->allowAllProperties();
        }
    }

    protected function initializeView(ViewInterface $view): void
    {
        $view->assign('contentElementData', $this->getContentObjectData());
    }

    /**
     * Lists the latest blog posts (limited, no pagination).
     */
    public function latestAction(?PostsDemand $demand = null): ResponseInterface
    {
        $demand = $this->prepareDemandObject($demand);
        $posts = $this->pageRepository->findPosts($this->pages, $demand);

        $this->view->assign('demand', $demand);
        $this->view->assign('posts', $posts);

        return $this->htmlResponse();
    }

    /**
     * Lists blog posts with pagination.
     */
    public function listAction(?PostsDemand $demand = null): ResponseInterface
    {
        $demand = $this->prepareDemandObject($demand);
        // Fetch all posts, the paginator slices them
        $demand->setLimit(0);

        $posts = $this->pageRepository->findPosts($this->pages, $demand);
        $paginator = new ArrayPaginator($posts, $demand->getCurrentPage(), $demand->getItemsPerPage());
        $pagination = new SimplePagination($paginator);

        $this->view->assign('demand', $demand);
        $this->view->assign('posts', $posts);
        $this->view->assign('paginator', $paginator);
        $this->view->assign('pagination', $pagination);

        return $this->htmlResponse();
    }

    /**
     * Completes the demand object with the plugin settings. A demand object
     * submitted with the request is only used if the plugin allows it.
     */
    protected function prepareDemandObject(?PostsDemand $demand = null): PostsDemand
    {
        $settings = $this->settings;
        $allowDemandOverwrite = (bool)($settings['allowDemandOverwrite'] ?? false);

        if (!$demand instanceof PostsDemand || !$allowDemandOverwrite) {
            $demand = new PostsDemand();
        }

        if (!$demand->hasCategories()) {
            $demand->setCategories($this->categories);
        }
        if (!$demand->hasExcluded()) {
            $demand->setExcluded(GeneralUtility::intExplode(',', (string)($settings['exclude'] ?? ''), true));
        }
        if (!$demand->hasLimit()) {
            $demand->setLimit((int)($settings['limit'] ?? 0));
        }
        if (!$demand->hasItemsPerPage()) {
            $demand->setItemsPerPage((int)($settings['itemsPerPage'] ?? 0));
        }
        if (!$demand->hasOffset()) {
            $demand->setOffset((int)($settings['offset'] ?? 0));
        }

        $archived = (string)($settings['archived'] ?? '');
        if (!$demand->isArchivedSet() && $archived === 'archived') {
            $demand->setArchived(true);
        }
        if (!$demand->isNonArchivedSet() && $archived === 'nonArchived') {
            $demand->setNonArchived(true);
        }

        $topPosts = (string)($settings['topPosts'] ?? '');
        if (!$demand->isTopPostSet() && $topPosts === 'topPosts') {
            $demand->setTopPost(true);
        }
        if (!$demand->isNonTopPostSet() && $topPosts === 'nonTopPosts') {
            $demand->setNonTopPost(true);
        }

        $sorting = (string)($settings['sorting'] ?? '');
        if (!$demand->hasOrder() && $sorting !== '') {
            $demand->setOrder($sorting);
        }
        $sortingDirection = (string)($settings['sortingDirection'] ?? '');
        if (!$demand->hasOrderDirection() && $sortingDirection !== '') {
            $demand->setOrderDirection($sortingDirection);
        }

        return $demand;
    }
}
