<?php

namespace Greenfieldr\Golb\Controller;

/*
 * This file is part of TYPO3 CMS-based extension "golb".
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 3
 * of the License, or any later version.
 */

use TYPO3\CMS\Core\Pagination\ArrayPaginator;
use TYPO3\CMS\Core\Pagination\SimplePagination;
use TYPO3\CMS\Extbase\Mvc\Exception\NoSuchArgumentException;
use Psr\Http\Message\ResponseInterface;
use Greenfieldr\Golb\Domain\Model\Dto\PostsDemand;
use Greenfieldr\Golb\Domain\Repository\CategoryRepository;
use Greenfieldr\Golb\Domain\Repository\PageRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\View\ViewInterface;

/**
 * Class BlogController
 *
 * @package Greenfieldr\Golb\Domain\Controller
 */
class BlogController extends BaseController
{

    /**
     * Contains array with pages to load blog posts from
     *
     * @var array $pages
     */
    protected $pages;

    /**
     * Contains categories to filter posts
     *
     * @var array $categories
     */
    protected $categories;

    /**
     * BlogController constructor.
     *
     * @param PageRepository $pageRepository
     * @param CategoryRepository $categoryRepository
     */
    public function __construct(protected PageRepository $pageRepository, protected CategoryRepository $categoryRepository)
    {
    }

    /**
     * Sets pages and categories properties
     *
     * @return void
     * @throws NoSuchArgumentException
     */
    #[\Override]
    public function initializeAction(): void
    {
        parent::initializeAction();

        $this->pages = array_map(trim(...), explode(',', (string) $this->contentObject->data['pages']));
        $this->categories = $this->categoryRepository->findByRelation($this->contentObject->data['uid'])->toArray();

        if($this->arguments->hasArgument('demand')) {
            $this->arguments->getArgument('demand')->getPropertyMappingConfiguration()
                ->allowAllProperties();
        }
    }

    /**
     * @param ViewInterface $view
     */
    protected function initializeView(ViewInterface $view): void
    {
        $view->assign('contentElementData', $this->contentObject->data);
    }

    /**
     * Lists latest blog posts
     *
     * @param PostsDemand|null $demand
     * @return ResponseInterface
     */
    public function latestAction(PostsDemand $demand = null): ResponseInterface
    {
        $demand = $this->prepareDemandObject($demand);

        $posts = $this->pageRepository->findPosts($this->pages, $demand);
        
        $this->view->assign('demand', $demand);
        $this->view->assign('posts', $posts);
        return $this->htmlResponse();
    }

    /**
     * Lists blog posts
     *
     * @param PostsDemand|null $demand
     * @return ResponseInterface
     */
    public function listAction(PostsDemand $demand = null): ResponseInterface
    {
        $demand = $this->prepareDemandObject($demand);

        // Reset limit to allow pagination of entries
        $demand->setLimit(0);

        $paginator = new ArrayPaginator(
            $this->pageRepository->findPosts($this->pages, $demand),
            $demand->getCurrentPage(),
            $demand->getItemsPerPage()
        );

        /**
         * ToDo: Feature: Implement SlidingWindowPagination as optional alternative
         */
        $pagination = new SimplePagination($paginator);

        $this->view->assign('pagination', $pagination);
        $this->view->assign('paginator', $paginator);
        $this->view->assign('demand', $demand);
        return $this->htmlResponse();
    }

    /**
     * @param PostsDemand|null $demand
     * @return PostsDemand
     */
    protected function prepareDemandObject(PostsDemand $demand = null): PostsDemand
    {
        // Use submitted demand object only if availalbe and allowDemandOverwrite is set.
        $demand = $demand instanceof PostsDemand && $this->settings['allowDemandOverwrite'] ? $demand : new PostsDemand();

        if(!$demand->hasCategories()) {
            $demand->setCategories($this->categories);
        }
        if(!$demand->hasExcluded()) {
            $excluded = GeneralUtility::trimExplode(',', $this->settings['exclude']);
            $demand->setExcluded(array_map(fn($item) => (int) $item, $excluded));
        }
        if(!$demand->hasLimit()) {
            $demand->setLimit($this->settings['limit'] ?? 0);
        }
        if(!$demand->hasItemsPerPage()) {
            $demand->setItemsPerPage($this->settings['itemsPerPage'] ?? 0);
        }
        if(!$demand->hasOffset()) {
            $demand->setOffset($this->settings['offset'] ?? 0);
        }
        if(!$demand->isArchivedSet() && $this->settings['archived'] === 'archived') {
            $demand->setArchived(true);
        }
        if(!$demand->isNonArchivedSet() && $this->settings['archived'] === 'nonArchived') {
            $demand->setNonArchived(true);
        }
        if(!$demand->isTopPostSet() && $this->settings['topPosts'] === 'topPosts') {
            $demand->setTopPost(true);
        }
        if(!$demand->isNonTopPostSet() && $this->settings['topPosts'] === 'nonTopPosts') {
            $demand->setNonTopPost(true);
        }
        if(!$demand->hasOrder() && $this->settings['sorting']) {
            $demand->setOrder($this->settings['sorting']);
        }
        if(!$demand->hasOrderDirection() && $this->settings['sortingDirection']) {
            $demand->setOrderDirection($this->settings['sortingDirection']);
        }

        return $demand;
    }
}