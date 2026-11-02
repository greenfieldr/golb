<?php

declare(strict_types=1);

/*
 * This file is part of the TYPO3 extension "golb".
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 3
 * of the License, or any later version.
 */

namespace Greenfieldr\Golb\Tests\Unit\Domain\Model;

use Greenfieldr\Golb\Domain\Model\Category;
use Greenfieldr\Golb\Domain\Model\Page;
use Greenfieldr\Golb\Domain\Model\Tag;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

final class TagAndCategoryTest extends UnitTestCase
{
    #[Test]
    public function pagesCanBeAddedToAndRemovedFromTag(): void
    {
        $tag = new Tag();
        $page = new Page();

        $tag->addPage($page);
        self::assertCount(1, $tag->getPages());
        self::assertTrue($tag->getPages()->contains($page));

        $tag->removePage($page);
        self::assertCount(0, $tag->getPages());
    }

    #[Test]
    public function categoryCanBeConstructedAndHasNoParentByDefault(): void
    {
        $category = new Category();

        self::assertNull($category->getParent());
        self::assertCount(0, $category->getSubCategories());
    }

    #[Test]
    public function subCategoriesCanBeAddedAndRemoved(): void
    {
        $parent = new Category();
        $child = new Category();
        $child->setParent($parent);

        $parent->addSubCategory($child);
        self::assertSame($parent, $child->getParent());
        self::assertCount(1, $parent->getSubCategories());

        $parent->removeSubCategory($child);
        self::assertCount(0, $parent->getSubCategories());
    }
}
