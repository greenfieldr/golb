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
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings;
use TYPO3\CMS\Extbase\Persistence\Repository;

/**
 * Finds blog posts (pages with the golb doktype) below given root pages.
 *
 * @extends Repository<Page>
 */
class PageRepository extends Repository
{
    public function __construct(private readonly ConnectionPool $connectionPool)
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
     * root pages themselves excluded). In a non-default language the uids of
     * the translated pages are returned.
     *
     * @param int[] $rootPages
     * @return int[]
     */
    public function findSubPagesByPageIds(array $rootPages): array
    {
        $parentIds = array_values(array_unique(array_filter(
            array_map(static fn($id): int => abs((int)$id), $rootPages)
        )));
        $pageIds = [];
        while ($parentIds !== []) {
            $queryBuilder = $this->connectionPool->getQueryBuilderForTable('pages');
            $children = $queryBuilder
                ->select('uid')
                ->from('pages')
                ->where(
                    $queryBuilder->expr()->in(
                        'pid',
                        $queryBuilder->createNamedParameter($parentIds, Connection::PARAM_INT_ARRAY)
                    ),
                    $queryBuilder->expr()->eq(
                        'sys_language_uid',
                        $queryBuilder->createNamedParameter(0, Connection::PARAM_INT)
                    )
                )
                ->executeQuery()
                ->fetchFirstColumn();
            // Guard against cycles (e.g. via mount points) by ignoring known uids
            $children = array_values(array_diff(array_map(intval(...), $children), $pageIds));
            $pageIds = array_merge($pageIds, $children);
            $parentIds = $children;
        }

        $language = $this->defaultQuerySettings->getLanguageAspect()->getContentId();
        if ($language > 0 && $pageIds !== []) {
            $queryBuilder = $this->connectionPool->getQueryBuilderForTable('pages');
            $pageIds = $queryBuilder
                ->select('uid')
                ->from('pages')
                ->where(
                    $queryBuilder->expr()->eq(
                        'sys_language_uid',
                        $queryBuilder->createNamedParameter($language, Connection::PARAM_INT)
                    ),
                    $queryBuilder->expr()->in(
                        'l10n_parent',
                        $queryBuilder->createNamedParameter($pageIds, Connection::PARAM_INT_ARRAY)
                    )
                )
                ->executeQuery()
                ->fetchFirstColumn();
        }

        return array_values(array_unique(array_map(intval(...), $pageIds)));
    }

    /**
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
                $query->in('uid', $pageIds),
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
