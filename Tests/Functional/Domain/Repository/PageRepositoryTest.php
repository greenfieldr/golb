<?php

declare(strict_types=1);

/*
 * This file is part of the TYPO3 extension "golb".
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 3
 * of the License, or any later version.
 */

namespace Greenfieldr\Golb\Tests\Functional\Domain\Repository;

use Greenfieldr\Golb\Domain\Model\Category;
use Greenfieldr\Golb\Domain\Model\Dto\PostsDemand;
use Greenfieldr\Golb\Domain\Model\Page;
use Greenfieldr\Golb\Domain\Repository\CategoryRepository;
use Greenfieldr\Golb\Domain\Repository\PageRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

#[CoversClass(PageRepository::class)]
#[CoversClass(CategoryRepository::class)]
#[CoversClass(Page::class)]
#[CoversClass(Category::class)]
final class PageRepositoryTest extends FunctionalTestCase
{
    protected array $coreExtensionsToLoad = ['typo3/cms-install'];

    protected array $testExtensionsToLoad = ['greenfieldr/golb'];

    private PageRepository $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->importCSVDataSet(__DIR__ . '/Fixtures/BlogPages.csv');
        $this->subject = $this->get(PageRepository::class);
    }

    /**
     * @param Page[] $posts
     * @return int[]
     */
    private function uids(array $posts): array
    {
        return array_map(static fn(Page $post): int => (int)$post->getUid(), $posts);
    }

    #[Test]
    public function findsPostsRecursivelyAndSortsByEffectiveDateDescendingByDefault(): void
    {
        $posts = $this->subject->findPosts([2], new PostsDemand());

        // Hidden posts and posts below hidden pages are not found
        self::assertSame([3, 6, 4], $this->uids($posts));
    }

    #[Test]
    public function rootPagesThemselvesAndPagesOfOtherTreesAreIgnored(): void
    {
        self::assertSame([10, 3, 6, 4], $this->uids($this->subject->findPosts([1], new PostsDemand())));
        self::assertSame([3, 6, 4], $this->uids($this->subject->findPosts([-2], new PostsDemand())));
        self::assertSame([], $this->subject->findPosts([], new PostsDemand()));
        self::assertSame([], $this->subject->findPosts([999], new PostsDemand()));
    }

    #[Test]
    public function sortsAscending(): void
    {
        $demand = (new PostsDemand())->setOrderDirection('ASC');

        self::assertSame([4, 6, 3], $this->uids($this->subject->findPosts([2], $demand)));
    }

    #[Test]
    public function sortsByCreationAndModificationDate(): void
    {
        $demand = (new PostsDemand())->setOrder(PostsDemand::ORDER_CREATION_DATE)->setOrderDirection('ASC');
        self::assertSame([3, 4, 6], $this->uids($this->subject->findPosts([2], $demand)));

        $demand = (new PostsDemand())->setOrder(PostsDemand::ORDER_MODIFICATION_DATE);
        self::assertSame([6, 4, 3], $this->uids($this->subject->findPosts([2], $demand)));
    }

    #[Test]
    public function sortsByTitle(): void
    {
        $demand = (new PostsDemand())->setOrder(PostsDemand::ORDER_TITLE)->setOrderDirection('ASC');

        self::assertSame([3, 4, 6], $this->uids($this->subject->findPosts([2], $demand)));
    }

    #[Test]
    public function sortsByAuthorWithEmptyAuthorsLast(): void
    {
        $demand = (new PostsDemand())->setOrder(PostsDemand::ORDER_AUTHOR)->setOrderDirection('ASC');

        self::assertSame([4, 3, 6], $this->uids($this->subject->findPosts([2], $demand)));
    }

    #[Test]
    public function appliesLimitAndOffset(): void
    {
        $demand = (new PostsDemand())->setLimit(2)->setOffset(1);

        self::assertSame([6, 4], $this->uids($this->subject->findPosts([2], $demand)));
    }

    #[Test]
    public function limitZeroReturnsAllPosts(): void
    {
        $demand = (new PostsDemand())->setLimit(0);

        self::assertSame([3, 6, 4], $this->uids($this->subject->findPosts([2], $demand)));
    }

    #[Test]
    public function excludesGivenPosts(): void
    {
        $demand = (new PostsDemand())->setExcluded([6]);

        self::assertSame([3, 4], $this->uids($this->subject->findPosts([2], $demand)));
    }

    #[Test]
    public function filtersArchivedAndNonArchivedPosts(): void
    {
        self::assertSame([4], $this->uids($this->subject->findPosts([2], (new PostsDemand())->setArchived(true))));
        self::assertSame([3, 6], $this->uids($this->subject->findPosts([2], (new PostsDemand())->setNonArchived(true))));
    }

    #[Test]
    public function filtersTopAndNonTopPosts(): void
    {
        self::assertSame([6], $this->uids($this->subject->findPosts([2], (new PostsDemand())->setTopPost(true))));
        self::assertSame([3, 4], $this->uids($this->subject->findPosts([2], (new PostsDemand())->setNonTopPost(true))));
    }

    #[Test]
    public function filtersByTags(): void
    {
        self::assertSame([3, 4], $this->uids($this->subject->findPosts([2], (new PostsDemand())->setTags([2]))));
        self::assertSame([3], $this->uids($this->subject->findPosts([2], (new PostsDemand())->setTag(1))));
        self::assertSame([], $this->uids($this->subject->findPosts([2], (new PostsDemand())->setTag(999))));
    }

    #[Test]
    public function filtersByCategoriesIncludingSubCategories(): void
    {
        $categoryRepository = $this->get(CategoryRepository::class);
        $news = $categoryRepository->findByUid(1);
        $tech = $categoryRepository->findByUid(2);
        self::assertInstanceOf(Category::class, $news);
        self::assertInstanceOf(Category::class, $tech);

        self::assertSame([3, 4], $this->uids($this->subject->findPosts([2], (new PostsDemand())->setCategories([$news]))));
        self::assertSame([3], $this->uids($this->subject->findPosts([2], (new PostsDemand())->setCategory($tech))));
    }

    #[Test]
    public function findSubPagesByPageIdsReturnsAllVisibleDescendants(): void
    {
        self::assertSame([3, 4, 5, 6], $this->subject->findSubPagesByPageIds([2]));
        self::assertSame([], $this->subject->findSubPagesByPageIds([]));
    }

    #[Test]
    public function findByTagsRanksPostsBySharedTags(): void
    {
        $related = $this->subject->findByTags([2], ['php', 'typo3'], [6]);

        self::assertCount(2, $related);
        self::assertSame(3, $related[0]['post']->getUid());
        self::assertSame(2, $related[0]['amount']);
        self::assertSame(4, $related[1]['post']->getUid());
        self::assertSame(1, $related[1]['amount']);

        $related = $this->subject->findByTags([2], ['php'], [3]);
        self::assertSame([], $related);
    }

    #[Test]
    public function categoriesOfAContentElementAreFoundInSortingOrder(): void
    {
        $categories = $this->get(CategoryRepository::class)->findByRelation(100);

        self::assertSame(['Tech', 'News'], array_map(static fn($category) => $category->getTitle(), $categories));
    }
}
