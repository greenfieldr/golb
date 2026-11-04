<?php

declare(strict_types=1);

/*
 * This file is part of the TYPO3 extension "golb".
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 3
 * of the License, or any later version.
 */

namespace Greenfieldr\Golb\Domain\Repository;

use Greenfieldr\Golb\Constants;
use Greenfieldr\Golb\Domain\Model\Category;
use Greenfieldr\Golb\Domain\Model\Dto\PostsDemand;
use Greenfieldr\Golb\Domain\Model\Page;
use Greenfieldr\Golb\Domain\Model\Tag;
use TYPO3\CMS\Core\Domain\Repository\PageRepository as CorePageRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings;
use TYPO3\CMS\Extbase\Persistence\Repository;

/**
 * Finds blog posts (pages with the golb doktype) below given root pages.
 *
 * The page tree below the root pages is walked with the core page repository,
 * which applies the frontend access rules (hidden, start and end time and
 * frontend groups of a page, "extend to subpages" of its parents, workspace
 * versions, mount points). The posts among these pages are loaded through
 * Extbase, which takes care of the language overlays and fallbacks.
 *
 * @extends Repository<Page>
 */
class PageRepository extends Repository
{
    /**
     * Levels of the page tree below a root page that are searched for posts
     */
    protected const TREE_DEPTH = 99;

    public function __construct(private readonly CorePageRepository $pageRepository)
    {
        parent::__construct();
    }

    public function initializeObject(): void
    {
        $querySettings = GeneralUtility::makeInstance(Typo3QuerySettings::class);
        $querySettings->setRespectStoragePage(false);
        $this->setDefaultQuerySettings($querySettings);
    }

    /**
     * Returns the blog posts below the given root pages, sorted, filtered and
     * sliced according to the demand object.
     *
     * @param int[] $rootPages
     * @return Page[]
     */
    public function findPosts(array $rootPages, PostsDemand $demand): array
    {
        $posts = $this->findPostsBelow($rootPages);
        $posts = $this->sortPosts($posts, $demand->getOrder(), $demand->getOrderDirection());
        $posts = $this->filterPosts($posts, $demand);

        return array_slice(
            $posts,
            $demand->getOffset(),
            $demand->getLimit() > 0 ? $demand->getLimit() : null
        );
    }

    /**
     * Returns blog posts sharing at least one of the given tag titles, best
     * matches (most shared tags) first.
     *
     * @param int[] $rootPages
     * @param string[] $tags Tag titles to match
     * @param int[] $excludeList Post uids to leave out, e.g. the current post
     * @return array<int, array{amount: int, post: Page}>
     */
    public function findByTags(array $rootPages, array $tags, array $excludeList = [], int $limit = 3): array
    {
        $matches = [];
        foreach ($this->findPostsBelow($rootPages) as $post) {
            if (in_array($post->getUid(), $excludeList, true)) {
                continue;
            }
            $amount = 0;
            /** @var Tag $tag */
            foreach ($post->getTags() as $tag) {
                if (in_array($tag->getTitle(), $tags, true)) {
                    $amount++;
                }
            }
            if ($amount > 0) {
                $matches[] = ['amount' => $amount, 'post' => $post];
            }
        }

        usort($matches, static fn(array $a, array $b): int => $b['amount'] <=> $a['amount']);

        return array_slice($matches, 0, $limit > 0 ? $limit : null);
    }

    /**
     * Returns the uids of all pages below the given root pages (recursively,
     * root pages themselves excluded) that are accessible for the current
     * frontend user. Hidden, time restricted and group restricted pages are
     * left out; if such a page has "extend to subpages" set, its whole
     * subtree is left out as well. Workspace versions and mount points are
     * resolved. The root pages themselves are not checked.
     *
     * The uids are those of the default language pages, also in a translated
     * frontend; Extbase overlays the translations when the posts are loaded.
     *
     * @param int[] $rootPages
     * @return int[]
     */
    public function findSubPagesByPageIds(array $rootPages): array
    {
        $pageIds = [];
        foreach ($this->toIntegerList(array_map(abs(...), $rootPages)) as $rootPage) {
            $pageIds = array_merge(
                $pageIds,
                $this->pageRepository->getDescendantPageIdsRecursive($rootPage, static::TREE_DEPTH)
            );
        }

        return $this->toIntegerList($pageIds);
    }

    /**
     * @param array<mixed> $values
     * @return list<int> the unique positive integers among the values, in order
     */
    protected function toIntegerList(array $values): array
    {
        $integers = [];
        foreach ($values as $value) {
            if (is_int($value) || (is_string($value) && is_numeric($value))) {
                $integer = (int)$value;
                if ($integer > 0) {
                    $integers[$integer] = $integer;
                }
            }
        }

        return array_values($integers);
    }

    /**
     * Loads the blog posts among the accessible pages below the root pages.
     * Default language posts are matched by their uid, translated posts
     * (Extbase selects them directly in the strict and free language modes)
     * by their translation parent, so Extbase applies the language overlay
     * and fallback rules of the current language aspect.
     *
     * @param int[] $rootPages
     * @return Page[]
     */
    protected function findPostsBelow(array $rootPages): array
    {
        $pageIds = $this->findSubPagesByPageIds($rootPages);
        if ($pageIds === []) {
            return [];
        }

        $query = $this->createQuery();
        $query->matching(
            $query->logicalAnd(
                $query->logicalOr(
                    $query->in('uid', $pageIds),
                    $query->in('l10n_parent', $pageIds)
                ),
                $query->equals('doktype', Constants::BLOG_POST_DOKTYPE)
            )
        );

        /** @var Page[] $posts */
        $posts = $query->execute()->toArray();

        return $posts;
    }

    /**
     * @param Page[] $posts
     * @return Page[]
     */
    protected function sortPosts(array $posts, string $order, string $direction): array
    {
        $comparator = match ($order) {
            PostsDemand::ORDER_DATE => static fn(Page $a, Page $b): int => $a->getEffectiveDate() <=> $b->getEffectiveDate(),
            PostsDemand::ORDER_CREATION_DATE => static fn(Page $a, Page $b): int => ($a->getCreationDate() ?? 0) <=> ($b->getCreationDate() ?? 0),
            PostsDemand::ORDER_MODIFICATION_DATE => static fn(Page $a, Page $b): int => ($a->getTstamp() ?? 0) <=> ($b->getTstamp() ?? 0),
            PostsDemand::ORDER_TITLE => static fn(Page $a, Page $b): int => strnatcasecmp($a->getTitle(), $b->getTitle()),
            PostsDemand::ORDER_AUTHOR => static function (Page $a, Page $b): int {
                // Posts without an author are listed last
                $authorA = $a->getAuthor() !== '' ? mb_strtolower($a->getAuthor()) : null;
                $authorB = $b->getAuthor() !== '' ? mb_strtolower($b->getAuthor()) : null;
                if ($authorA === null || $authorB === null) {
                    return ($authorA === null ? 1 : 0) <=> ($authorB === null ? 1 : 0);
                }
                return strnatcasecmp($authorA, $authorB);
            },
            default => null,
        };

        if ($comparator === null) {
            return $posts;
        }

        usort($posts, $comparator);

        if (strtoupper($direction) === 'DESC') {
            $posts = array_reverse($posts);
        }

        return $posts;
    }

    /**
     * @param Page[] $posts
     * @return Page[]
     */
    protected function filterPosts(array $posts, PostsDemand $demand): array
    {
        $categoryIds = $this->resolveCategoryIds($demand);
        $tagIds = $demand->getTags();
        $excluded = $demand->getExcluded();

        $posts = array_filter(
            $posts,
            function (Page $post) use ($demand, $categoryIds, $tagIds, $excluded): bool {
                if (in_array($post->getUid(), $excluded, true)) {
                    return false;
                }
                if ($demand->isArchived() && !$post->isArchived()) {
                    return false;
                }
                if ($demand->isNonArchived() && $post->isArchived()) {
                    return false;
                }
                if ($demand->isTopPost() && !$post->isTopPost()) {
                    return false;
                }
                if ($demand->isNonTopPost() && $post->isTopPost()) {
                    return false;
                }
                if ($categoryIds !== [] && !$this->hasAnyCategory($post, $categoryIds)) {
                    return false;
                }
                if ($tagIds !== [] && !$this->hasAnyTag($post, $tagIds)) {
                    return false;
                }
                return true;
            }
        );

        return array_values($posts);
    }

    /**
     * Collects the uids of the demanded categories including all their sub
     * categories.
     *
     * @return int[]
     */
    protected function resolveCategoryIds(PostsDemand $demand): array
    {
        $categories = $demand->getCategories();
        if ($demand->getCategory() instanceof Category) {
            $categories[] = $demand->getCategory();
        }

        $categoryIds = [];
        $this->collectCategoryIds($categories, $categoryIds);

        return array_values(array_unique($categoryIds));
    }

    /**
     * @param iterable<Category> $categories
     * @param int[] $categoryIds
     */
    protected function collectCategoryIds(iterable $categories, array &$categoryIds): void
    {
        foreach ($categories as $category) {
            $uid = $category->getUid();
            if ($uid === null || in_array($uid, $categoryIds, true)) {
                continue;
            }
            $categoryIds[] = $uid;
            $this->collectCategoryIds($category->getSubCategories(), $categoryIds);
        }
    }

    /**
     * @param int[] $categoryIds
     */
    protected function hasAnyCategory(Page $post, array $categoryIds): bool
    {
        /** @var Category $category */
        foreach ($post->getCategories() as $category) {
            if (in_array($category->getUid(), $categoryIds, true)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param int[] $tagIds
     */
    protected function hasAnyTag(Page $post, array $tagIds): bool
    {
        /** @var Tag $tag */
        foreach ($post->getTags() as $tag) {
            if (in_array($tag->getUid(), $tagIds, true)) {
                return true;
            }
        }

        return false;
    }
}
