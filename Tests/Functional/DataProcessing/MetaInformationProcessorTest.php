<?php

declare(strict_types=1);

/*
 * This file is part of the TYPO3 extension "golb".
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 3
 * of the License, or any later version.
 */

namespace Greenfieldr\Golb\Tests\Functional\DataProcessing;

use Greenfieldr\Golb\DataProcessing\MetaInformationProcessor;
use Greenfieldr\Golb\Domain\Model\Page;
use Greenfieldr\Golb\Domain\Repository\PageRepository;
use Greenfieldr\Golb\ViewHelpers\RelatedPostsViewHelper;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\TestingFramework\Core\Functional\Framework\Frontend\InternalRequest;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

/**
 * Renders pages with the processor in the page template and the detail
 * rendering lib.golb.post. The fixture (see BlogControllerTest) has the
 * posts "First post" (page 3, by Zoe, tags php and typo3, published 5000),
 * "Second post" (page 4, archived, category News, tag typo3) and "Third
 * post" (page 5, top post, no tags) below the blog page 2.
 */
#[CoversClass(MetaInformationProcessor::class)]
#[CoversClass(RelatedPostsViewHelper::class)]
#[CoversClass(PageRepository::class)]
#[CoversClass(Page::class)]
final class MetaInformationProcessorTest extends FunctionalTestCase
{
    protected array $testExtensionsToLoad = ['greenfieldr/golb'];

    protected array $coreExtensionsToLoad = ['typo3/cms-install', 'typo3/cms-fluid-styled-content'];

    protected array $pathsToLinkInTestInstance = [
        'typo3conf/ext/golb/Tests/Functional/Controller/Fixtures/Sites/' => 'typo3conf/sites',
    ];

    protected function setUp(): void
    {
        parent::setUp();

        $this->importCSVDataSet(__DIR__ . '/../Fixtures/BlogSite.csv');
        $this->get(ConnectionPool::class)->getConnectionForTable('tt_content')->insert('tt_content', [
            'uid' => 200,
            'pid' => 3,
            'colPos' => 0,
            'sorting' => 1,
            'CType' => 'text',
            'header' => 'Body',
            'bodytext' => '<p>The content of the first post.</p>',
        ]);
        $this->setUpFrontendRootPage(1, [
            'constants' => [
                'EXT:fluid_styled_content/Configuration/TypoScript/constants.typoscript',
                'EXT:golb/Configuration/TypoScript/constants.typoscript',
            ],
            'setup' => [
                'EXT:fluid_styled_content/Configuration/TypoScript/setup.typoscript',
                'EXT:golb/Configuration/TypoScript/setup.typoscript',
                'EXT:golb/Tests/Functional/DataProcessing/Fixtures/Rendering.typoscript',
            ],
        ]);
    }

    private function render(int $pageId): string
    {
        return (string)$this->executeFrontendSubRequest((new InternalRequest())->withPageId($pageId))->getBody();
    }

    #[Test]
    public function processorProvidesThePostOfTheCurrentPage(): void
    {
        $html = $this->render(3);

        self::assertStringContainsString('[blogPost:First post:41]', $html);
    }

    #[Test]
    public function detailRenderingShowsTheArticleWithContentAndMetaData(): void
    {
        $html = $this->render(3);

        self::assertStringContainsString('<article class="golb-post golb-post--detail">', $html);
        self::assertStringContainsString('<h1 class="golb-post__title">First post</h1>', $html);
        self::assertStringContainsString('by Zoe', $html);
        self::assertStringContainsString('datetime="1970-01-01"', $html);
        self::assertStringContainsString('<p>The content of the first post.</p>', $html);
        self::assertStringContainsString('<span class="golb-term">typo3</span>', $html);
        self::assertStringContainsString('Resources/Public/Css/golb.css', $html);
    }

    #[Test]
    public function detailRenderingAddsArticleMetaTags(): void
    {
        $html = $this->render(3);

        self::assertStringContainsString('<meta property="og:type" content="article"', $html);
        self::assertStringContainsString('<meta property="article:published_time" content="' . date('c', 5000) . '"', $html);
        self::assertStringContainsString('<meta property="article:modified_time" content="' . date('c', 1000) . '"', $html);
        self::assertStringContainsString('<meta property="article:author" content="Zoe"', $html);
        self::assertStringContainsString('<meta property="article:tag" content="php"', $html);
        self::assertStringContainsString('<meta property="article:tag" content="typo3"', $html);
        self::assertStringNotContainsString('article:section', $html, 'the first post has no category');
    }

    #[Test]
    public function detailRenderingListsRelatedPostsSharingTags(): void
    {
        // The first and the second post share the tag "typo3", the third post has no tags
        $html = $this->render(3);

        self::assertStringContainsString('<h2 class="golb-related__title"', $html);
        self::assertStringContainsString('Related posts', $html);
        self::assertStringContainsString('Second post', $html);
        self::assertStringNotContainsString('Third post', $html);

        $html = $this->render(5);

        self::assertStringNotContainsString('golb-related', $html);
    }

    #[Test]
    public function detailRenderingPrefersHandPickedRelatedPosts(): void
    {
        $this->get(ConnectionPool::class)->getConnectionForTable('tx_golb_page_related_page_mm')->insert('tx_golb_page_related_page_mm', [
            'uid_local' => 3,
            'uid_foreign' => 5,
            'sorting' => 1,
            'sorting_foreign' => 1,
        ]);
        $this->get(ConnectionPool::class)->getConnectionForTable('pages')->update('pages', ['tx_golb_related' => 1], ['uid' => 3]);

        $html = $this->render(3);

        self::assertStringContainsString('Related posts', $html);
        self::assertStringContainsString('Third post', $html);
        self::assertStringNotContainsString('Second post', $html);
    }

    #[Test]
    public function archivedAndTopPostsAreFlagged(): void
    {
        $html = $this->render(4);

        self::assertStringContainsString('<article class="golb-post golb-post--detail golb-post--archived">', $html);
        self::assertStringContainsString('<span class="golb-flag golb-flag--archived">Archived</span>', $html);
        self::assertStringContainsString('<meta property="article:section" content="News"', $html);
        self::assertStringContainsString('<span class="golb-term">News</span>', $html);
    }

    #[Test]
    public function ordinaryPagesGetNeitherArticleMarkupNorMetaTags(): void
    {
        $html = $this->render(2);

        self::assertStringContainsString('[blogPost:Blog:1]', $html);
        self::assertStringContainsString('<article class="golb-post golb-post--detail">', $html, 'the lib renders whatever page it is used on');
        self::assertStringNotContainsString('og:type', $html);
        self::assertStringNotContainsString('article:published_time', $html);
    }
}
