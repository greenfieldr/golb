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

use Greenfieldr\Golb\Domain\Model\Page;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

final class PageTest extends UnitTestCase
{
    #[Test]
    public function effectiveDatePrefersPublishDate(): void
    {
        $page = new Page();
        $page->setCreationDate(100);
        $page->setPublishDate(200);

        self::assertSame(200, $page->getEffectiveDate());
    }

    #[Test]
    public function effectiveDateFallsBackToCreationDate(): void
    {
        $page = new Page();
        $page->setCreationDate(100);

        self::assertSame(100, $page->getEffectiveDate());

        $page->setPublishDate(0);
        self::assertSame(100, $page->getEffectiveDate());
    }

    #[Test]
    public function effectiveDateIsZeroWithoutDates(): void
    {
        self::assertSame(0, (new Page())->getEffectiveDate());
    }

    #[Test]
    public function navigationTitleFallsBackToTitle(): void
    {
        $page = new Page();
        $page->setTitle('Title');

        self::assertSame('Title', $page->getNavigationTitle());

        $page->setNavTitle('Nav');
        self::assertSame('Nav', $page->getNavigationTitle());
    }

    #[Test]
    public function flagsDefaultToFalse(): void
    {
        $page = new Page();

        self::assertFalse($page->isHidden());
        self::assertFalse($page->isDeleted());
        self::assertFalse($page->isNavHide());
        self::assertFalse($page->isExtendToSubpages());
        self::assertFalse($page->isNoSearch());
        self::assertFalse($page->isSiteRoot());
        self::assertFalse($page->isArchived());
        self::assertFalse($page->isTopPost());
        self::assertNull($page->getAuthorImage());
    }

    #[Test]
    public function relationsAreInitialised(): void
    {
        $page = new Page();

        self::assertCount(0, $page->getCategories());
        self::assertCount(0, $page->getTags());
        self::assertCount(0, $page->getRelatedPages());
        self::assertCount(0, $page->getSubpages());
        self::assertCount(0, $page->getContentElements());
        self::assertCount(0, $page->getMedia());
    }
}
