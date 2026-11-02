<?php

declare(strict_types=1);

/*
 * This file is part of the TYPO3 extension "golb".
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 3
 * of the License, or any later version.
 */

namespace Greenfieldr\Golb\Tests\Unit\Domain\Model\Dto;

use Greenfieldr\Golb\Domain\Model\Category;
use Greenfieldr\Golb\Domain\Model\Dto\PostsDemand;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

#[CoversClass(PostsDemand::class)]
final class PostsDemandTest extends UnitTestCase
{
    #[Test]
    public function defaultsApplyWhenNothingIsSet(): void
    {
        $demand = new PostsDemand();

        self::assertFalse($demand->hasLimit());
        self::assertSame(PostsDemand::LIMIT_DEFAULT, $demand->getLimit());
        self::assertFalse($demand->hasOffset());
        self::assertSame(PostsDemand::OFFSET_DEFAULT, $demand->getOffset());
        self::assertFalse($demand->hasOrder());
        self::assertSame(PostsDemand::ORDER_DEFAULT, $demand->getOrder());
        self::assertFalse($demand->hasOrderDirection());
        self::assertSame(PostsDemand::ORDER_DIRECTION_DEFAULT, $demand->getOrderDirection());
        self::assertFalse($demand->isArchivedSet());
        self::assertFalse($demand->isArchived());
        self::assertFalse($demand->isNonArchivedSet());
        self::assertFalse($demand->isTopPostSet());
        self::assertFalse($demand->isNonTopPostSet());
        self::assertFalse($demand->hasCategories());
        self::assertFalse($demand->hasTags());
        self::assertFalse($demand->hasExcluded());
        self::assertSame(1, $demand->getCurrentPage());
        self::assertFalse($demand->hasItemsPerPage());
        self::assertSame(PostsDemand::ITEMS_PER_PAGE_DEFAULT, $demand->getItemsPerPage());
    }

    #[Test]
    public function settersMarkValuesAsSet(): void
    {
        $demand = (new PostsDemand())
            ->setLimit(5)
            ->setOffset(2)
            ->setOrder(PostsDemand::ORDER_TITLE)
            ->setOrderDirection('asc')
            ->setArchived(true)
            ->setItemsPerPage(7)
            ->setCurrentPage(3);

        self::assertTrue($demand->hasLimit());
        self::assertSame(5, $demand->getLimit());
        self::assertTrue($demand->hasOffset());
        self::assertSame(2, $demand->getOffset());
        self::assertTrue($demand->hasOrder());
        self::assertSame('title', $demand->getOrder());
        self::assertTrue($demand->hasOrderDirection());
        self::assertSame('ASC', $demand->getOrderDirection());
        self::assertTrue($demand->isArchivedSet());
        self::assertTrue($demand->isArchived());
        self::assertTrue($demand->hasItemsPerPage());
        self::assertSame(7, $demand->getItemsPerPage());
        self::assertSame(3, $demand->getCurrentPage());
    }

    #[Test]
    public function currentPageIsNeverBelowOne(): void
    {
        $demand = (new PostsDemand())->setCurrentPage(0);

        self::assertSame(1, $demand->getCurrentPage());
    }

    #[Test]
    public function singleTagIsMergedIntoTagsWithoutDuplicates(): void
    {
        $demand = (new PostsDemand())->setTags([1, 2])->setTag(3);

        self::assertTrue($demand->hasTags());
        // Repeated calls must not accumulate the single tag
        self::assertCount(3, $demand->getTags());
        self::assertSame([1, 2, 3], $demand->getTags());

        $demand->setTag(2);
        self::assertSame([1, 2], $demand->getTags());
    }

    #[Test]
    public function singleTagAloneCountsAsTagFilter(): void
    {
        $demand = (new PostsDemand())->setTag(4);

        self::assertTrue($demand->hasTags());
        self::assertSame([4], $demand->getTags());
        self::assertSame(4, $demand->getTag());

        $demand->setTag(null);
        self::assertFalse($demand->hasTags());
    }

    #[Test]
    public function flagsCanBeSetIndividually(): void
    {
        $demand = (new PostsDemand())->setNonArchived(true)->setTopPost(true)->setNonTopPost(false)->setExcluded([3]);

        self::assertTrue($demand->isNonArchivedSet());
        self::assertTrue($demand->isNonArchived());
        self::assertTrue($demand->isTopPostSet());
        self::assertTrue($demand->isTopPost());
        self::assertTrue($demand->isNonTopPostSet());
        self::assertFalse($demand->isNonTopPost());
        self::assertTrue($demand->hasExcluded());
        self::assertSame([3], $demand->getExcluded());
        self::assertFalse($demand->isArchivedSet());
    }

    #[Test]
    public function categoriesAndSingleCategoryAreStored(): void
    {
        $category = new Category();
        $category->setTitle('News');

        $demand = (new PostsDemand())->setCategories([$category])->setCategory($category);

        self::assertTrue($demand->hasCategories());
        self::assertSame([$category], $demand->getCategories());
        self::assertSame($category, $demand->getCategory());
    }
}
