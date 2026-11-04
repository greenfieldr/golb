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
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Context\LanguageAspect;
use TYPO3\CMS\Core\Context\UserAspect;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Authentication\FrontendUserAuthentication;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

#[CoversClass(PageRepository::class)]
#[CoversClass(CategoryRepository::class)]
#[CoversClass(Page::class)]
#[CoversClass(Category::class)]
final class PageRepositoryTest extends FunctionalTestCase
{
    protected array $testExtensionsToLoad = ['greenfieldr/golb'];

    protected function setUp(): void
    {
        parent::setUp();
        $this->importCSVDataSet(__DIR__ . '/Fixtures/BlogPages.csv');
    }

    /**
     * The repository is created on first access: its query settings take the
     * language aspect from the context at that moment, so tests that change
     * the context do so before calling this method.
     */
    private function subject(): PageRepository
    {
        return $this->get(PageRepository::class);
    }

    /**
     * Simulates a logged in website user (member of the special group -2
     * "show at any login") in the context.
     */
    private function logInFrontendUser(): void
    {
        $frontendUser = GeneralUtility::makeInstance(FrontendUserAuthentication::class);
        $frontendUser->user = ['uid' => 1, 'username' => 'member'];
        $this->get(Context::class)->setAspect('frontend.user', new UserAspect($frontendUser));
    }

    private function setLanguage(string $overlayType): void
    {
        $this->get(Context::class)->setAspect(
            'language',
            new LanguageAspect(1, 1, $overlayType, $overlayType === LanguageAspect::OVERLAYS_MIXED ? [0] : [])
        );
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
        $posts = $this->subject()->findPosts([2], new PostsDemand());

        // Hidden posts and posts below hidden pages with "extend to subpages" are not found
        self::assertSame([3, 6, 4], $this->uids($posts));
    }

    #[Test]
    public function rootPagesThemselvesAndPagesOfOtherTreesAreIgnored(): void
    {
        self::assertSame([10, 3, 6, 4], $this->uids($this->subject()->findPosts([1], new PostsDemand())));
        self::assertSame([3, 6, 4], $this->uids($this->subject()->findPosts([-2], new PostsDemand())));
        self::assertSame([], $this->subject()->findPosts([], new PostsDemand()));
        self::assertSame([], $this->subject()->findPosts([999], new PostsDemand()));
    }

    #[Test]
    public function sortsAscending(): void
    {
        $demand = (new PostsDemand())->setOrderDirection('ASC');

        self::assertSame([4, 6, 3], $this->uids($this->subject()->findPosts([2], $demand)));
    }

    #[Test]
    public function sortsByCreationAndModificationDate(): void
    {
        $demand = (new PostsDemand())->setOrder(PostsDemand::ORDER_CREATION_DATE)->setOrderDirection('ASC');
        self::assertSame([3, 4, 6], $this->uids($this->subject()->findPosts([2], $demand)));

        $demand = (new PostsDemand())->setOrder(PostsDemand::ORDER_MODIFICATION_DATE);
        self::assertSame([6, 4, 3], $this->uids($this->subject()->findPosts([2], $demand)));
    }

    #[Test]
    public function sortsByTitle(): void
    {
        $demand = (new PostsDemand())->setOrder(PostsDemand::ORDER_TITLE)->setOrderDirection('ASC');

        self::assertSame([3, 4, 6], $this->uids($this->subject()->findPosts([2], $demand)));
    }

    #[Test]
    public function sortsByAuthorWithEmptyAuthorsLast(): void
    {
        $demand = (new PostsDemand())->setOrder(PostsDemand::ORDER_AUTHOR)->setOrderDirection('ASC');

        self::assertSame([4, 3, 6], $this->uids($this->subject()->findPosts([2], $demand)));
    }

    #[Test]
    public function appliesLimitAndOffset(): void
    {
        $demand = (new PostsDemand())->setLimit(2)->setOffset(1);

        self::assertSame([6, 4], $this->uids($this->subject()->findPosts([2], $demand)));
    }

    #[Test]
    public function limitZeroReturnsAllPosts(): void
    {
        $demand = (new PostsDemand())->setLimit(0);

        self::assertSame([3, 6, 4], $this->uids($this->subject()->findPosts([2], $demand)));
    }

    #[Test]
    public function excludesGivenPosts(): void
    {
        $demand = (new PostsDemand())->setExcluded([6]);

        self::assertSame([3, 4], $this->uids($this->subject()->findPosts([2], $demand)));
    }

    #[Test]
    public function filtersArchivedAndNonArchivedPosts(): void
    {
        self::assertSame([4], $this->uids($this->subject()->findPosts([2], (new PostsDemand())->setArchived(true))));
        self::assertSame([3, 6], $this->uids($this->subject()->findPosts([2], (new PostsDemand())->setNonArchived(true))));
    }

    #[Test]
    public function filtersTopAndNonTopPosts(): void
    {
        self::assertSame([6], $this->uids($this->subject()->findPosts([2], (new PostsDemand())->setTopPost(true))));
        self::assertSame([3, 4], $this->uids($this->subject()->findPosts([2], (new PostsDemand())->setNonTopPost(true))));
    }

    #[Test]
    public function filtersByTags(): void
    {
        self::assertSame([3, 4], $this->uids($this->subject()->findPosts([2], (new PostsDemand())->setTags([2]))));
        self::assertSame([3], $this->uids($this->subject()->findPosts([2], (new PostsDemand())->setTag(1))));
        self::assertSame([], $this->uids($this->subject()->findPosts([2], (new PostsDemand())->setTag(999))));
    }

    #[Test]
    public function filtersByCategoriesIncludingSubCategories(): void
    {
        $categoryRepository = $this->get(CategoryRepository::class);
        $news = $categoryRepository->findByUid(1);
        $tech = $categoryRepository->findByUid(2);
        self::assertInstanceOf(Category::class, $news);
        self::assertInstanceOf(Category::class, $tech);

        self::assertSame([3, 4], $this->uids($this->subject()->findPosts([2], (new PostsDemand())->setCategories([$news]))));
        self::assertSame([3], $this->uids($this->subject()->findPosts([2], (new PostsDemand())->setCategory($tech))));
    }

    #[Test]
    public function findSubPagesByPageIdsReturnsAllVisibleDescendants(): void
    {
        self::assertEqualsCanonicalizing([3, 4, 5, 6], $this->subject()->findSubPagesByPageIds([2]));
        self::assertEqualsCanonicalizing([2, 3, 4, 5, 6, 10], $this->subject()->findSubPagesByPageIds([1, -2, 2]));
        self::assertSame([], $this->subject()->findSubPagesByPageIds([]));
        self::assertSame([], $this->subject()->findSubPagesByPageIds([0, 999]));
    }

    #[Test]
    public function postsBelowAGroupRestrictedPageWithExtendToSubpagesAreNotListed(): void
    {
        $this->importCSVDataSet(__DIR__ . '/Fixtures/RestrictedPages.csv');

        $posts = $this->uids($this->subject()->findPosts([20], new PostsDemand()));

        self::assertContains(21, $posts, 'the public post is listed');
        self::assertNotContains(23, $posts, 'the post below the members area (fe_group -2, extend to subpages) is not listed');
        self::assertNotContains(24, $posts, 'the post restricted to logged in users is not listed');
    }

    #[Test]
    public function postsBelowRestrictedOrHiddenPagesWithoutExtendToSubpagesStayListed(): void
    {
        $this->importCSVDataSet(__DIR__ . '/Fixtures/RestrictedPages.csv');

        // Like the TYPO3 frontend, the access rules of a page apply to its sub
        // pages only with "extend to subpages"; these posts are reachable by URL
        self::assertSame([28, 26, 21], $this->uids($this->subject()->findPosts([20], new PostsDemand())));
    }

    #[Test]
    public function loggedInFrontendUsersSeeThePostsOfTheirGroups(): void
    {
        $this->importCSVDataSet(__DIR__ . '/Fixtures/RestrictedPages.csv');
        $this->logInFrontendUser();

        self::assertSame([28, 26, 24, 23, 21], $this->uids($this->subject()->findPosts([20], new PostsDemand())));
    }

    #[Test]
    public function findSubPagesByPageIdsAppliesTheFrontendAccessRules(): void
    {
        $this->importCSVDataSet(__DIR__ . '/Fixtures/RestrictedPages.csv');

        self::assertEqualsCanonicalizing([21, 26, 28], $this->subject()->findSubPagesByPageIds([20]));

        $this->logInFrontendUser();

        self::assertEqualsCanonicalizing([21, 22, 23, 24, 26, 27, 28], $this->subject()->findSubPagesByPageIds([20]));
    }

    #[Test]
    public function untranslatedPostsStayVisibleWhenTheLanguageFallsBackToTheDefaultLanguage(): void
    {
        $this->importCSVDataSet(__DIR__ . '/Fixtures/TranslatedPages.csv');
        $this->setLanguage(LanguageAspect::OVERLAYS_MIXED);

        $posts = $this->subject()->findPosts([2], new PostsDemand());

        self::assertSame([3, 6, 4], $this->uids($posts));
        self::assertSame(['Beitrag A', 'Post C (nested)', 'Post B'], array_map(static fn(Page $post): string => $post->getTitle(), $posts));
        self::assertSame(13, $posts[0]->_getProperty('_localizedUid'), 'the translated post is overlaid');
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function languageModesHidingUntranslatedPostsDataProvider(): iterable
    {
        yield 'strict' => [LanguageAspect::OVERLAYS_ON];
        yield 'strict with floating records' => [LanguageAspect::OVERLAYS_ON_WITH_FLOATING];
        yield 'free' => [LanguageAspect::OVERLAYS_OFF];
    }

    #[Test]
    #[DataProvider('languageModesHidingUntranslatedPostsDataProvider')]
    public function untranslatedPostsAreHiddenInStrictAndFreeLanguageModes(string $overlayType): void
    {
        $this->importCSVDataSet(__DIR__ . '/Fixtures/TranslatedPages.csv');
        $this->setLanguage($overlayType);

        $posts = $this->subject()->findPosts([2], new PostsDemand());

        self::assertSame([3], $this->uids($posts));
        self::assertSame('Beitrag A', $posts[0]->getTitle());
        self::assertSame(13, $posts[0]->_getProperty('_localizedUid'));
    }

    #[Test]
    public function findSubPagesByPageIdsReturnsDefaultLanguageUidsInTranslatedFrontends(): void
    {
        $this->importCSVDataSet(__DIR__ . '/Fixtures/TranslatedPages.csv');
        $this->setLanguage(LanguageAspect::OVERLAYS_MIXED);

        self::assertEqualsCanonicalizing([3, 4, 5, 6], $this->subject()->findSubPagesByPageIds([2]));
    }

    #[Test]
    public function findByTagsRanksPostsBySharedTags(): void
    {
        $related = $this->subject()->findByTags([2], ['php', 'typo3'], [6]);

        self::assertCount(2, $related);
        self::assertSame(3, $related[0]['post']->getUid());
        self::assertSame(2, $related[0]['amount']);
        self::assertSame(4, $related[1]['post']->getUid());
        self::assertSame(1, $related[1]['amount']);

        $related = $this->subject()->findByTags([2], ['php'], [3]);
        self::assertSame([], $related);
    }

    #[Test]
    public function categoriesOfAContentElementAreFoundInSortingOrder(): void
    {
        $categories = $this->get(CategoryRepository::class)->findByRelation(100);

        self::assertSame(['Tech', 'News'], array_map(static fn($category) => $category->getTitle(), $categories));
    }
}
