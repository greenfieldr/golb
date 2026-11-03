<?php

declare(strict_types=1);

/*
 * This file is part of the TYPO3 extension "golb".
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 3
 * of the License, or any later version.
 */

namespace Greenfieldr\Golb\Tests\Functional\Controller;

use Greenfieldr\Golb\Controller\BaseController;
use Greenfieldr\Golb\Controller\BlogController;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\TestingFramework\Core\Functional\Framework\Frontend\InternalRequest;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

/**
 * Renders the plugins through the frontend and checks the HTML output.
 */
#[CoversClass(BlogController::class)]
#[CoversClass(BaseController::class)]
final class BlogControllerTest extends FunctionalTestCase
{
    protected array $testExtensionsToLoad = ['greenfieldr/golb'];

    protected array $coreExtensionsToLoad = ['typo3/cms-install', 'typo3/cms-fluid-styled-content'];

    protected array $pathsToLinkInTestInstance = [
        'typo3conf/ext/golb/Tests/Functional/Controller/Fixtures/Sites/' => 'typo3conf/sites',
    ];

    protected array $configurationToUseInTestInstance = [
        'FE' => [
            'cacheHash' => [
                'enforceValidation' => false,
            ],
        ],
    ];

    protected function setUp(): void
    {
        parent::setUp();

        $this->importCSVDataSet(__DIR__ . '/../Fixtures/BlogSite.csv');
        $this->setUpFrontendRootPage(1, [
            'constants' => [
                'EXT:fluid_styled_content/Configuration/TypoScript/constants.typoscript',
                'EXT:golb/Configuration/TypoScript/constants.typoscript',
            ],
            'setup' => [
                'EXT:fluid_styled_content/Configuration/TypoScript/setup.typoscript',
                'EXT:golb/Configuration/TypoScript/setup.typoscript',
                'EXT:golb/Tests/Functional/Controller/Fixtures/TypoScript/Rendering.typoscript',
            ],
        ]);
    }

    /**
     * Creates a plugin content element on the root page.
     *
     * @param array<string, string|int> $settings FlexForm settings
     * @param int[] $categories uids of categories selected in the plugin
     */
    private function createPlugin(string $cType, string $pages, array $settings = [], array $categories = [], int $uid = 100): void
    {
        $fields = '';
        foreach ($settings as $name => $value) {
            $fields .= sprintf(
                '<field index="settings.%s"><value index="vDEF">%s</value></field>',
                htmlspecialchars($name, ENT_QUOTES),
                htmlspecialchars((string)$value, ENT_QUOTES)
            );
        }
        $flexForm = '<?xml version="1.0" encoding="utf-8" standalone="yes" ?><T3FlexForms><data>'
            . '<sheet index="sDEF"><language index="lDEF">' . $fields . '</language></sheet>'
            . '</data></T3FlexForms>';

        $connectionPool = $this->get(ConnectionPool::class);
        $connectionPool->getConnectionForTable('tt_content')->insert('tt_content', [
            'uid' => $uid,
            'pid' => 1,
            'colPos' => 0,
            'sorting' => $uid,
            'CType' => $cType,
            'header' => 'Plugin',
            'pages' => $pages,
            'pi_flexform' => $flexForm,
            'categories' => count($categories),
        ]);
        foreach ($categories as $sorting => $categoryUid) {
            $connectionPool->getConnectionForTable('sys_category_record_mm')->insert('sys_category_record_mm', [
                'uid_local' => $categoryUid,
                'uid_foreign' => $uid,
                'tablenames' => 'tt_content',
                'fieldname' => 'categories',
                'sorting' => $sorting + 1,
                'sorting_foreign' => $sorting + 1,
            ]);
        }
    }

    /**
     * @param array<string, int|string> $queryParameters flat parameter names, e.g. "tx_golb_listview[demand][tag]";
     *                                                   "golbSettings" overrides plugin.tx_golb.settings for the
     *                                                   request, see the Rendering.typoscript fixture
     */
    private function render(array $queryParameters = []): string
    {
        $request = (new InternalRequest())->withPageId(1);
        foreach ($queryParameters as $name => $value) {
            $request = $request->withQueryParameter($name, $value);
        }

        return (string)$this->executeFrontendSubRequest($request)->getBody();
    }

    #[Test]
    public function listViewRendersPostsNewestFirst(): void
    {
        $this->createPlugin('golb_listview', '2');

        $html = $this->render();

        self::assertStringContainsString('First post', $html);
        self::assertStringContainsString('Second post', $html);
        self::assertStringContainsString('Third post', $html);
        self::assertLessThan(strpos($html, 'Third post'), strpos($html, 'First post'));
        self::assertLessThan(strpos($html, 'Second post'), strpos($html, 'Third post'));
        self::assertSame(3, substr_count($html, '<article class="golb-post golb-post--teaser'));
        self::assertStringNotContainsString('golb-pagination', $html);
    }

    #[Test]
    public function listViewShowsMessageWhenNoPostsExist(): void
    {
        $this->createPlugin('golb_listview', '6');

        $html = $this->render();

        self::assertStringContainsString('No blog posts found.', $html);
        self::assertStringNotContainsString('golb-post', $html);
    }

    #[Test]
    public function listViewPaginatesAndFollowsPageArgument(): void
    {
        $this->createPlugin('golb_listview', '2', ['itemsPerPage' => 2]);

        $html = $this->render();

        self::assertSame(2, substr_count($html, '<article class="golb-post golb-post--teaser'));
        self::assertStringContainsString('golb-pagination', $html);
        self::assertStringContainsString('First post', $html);
        self::assertStringNotContainsString('Second post', $html);
        self::assertStringContainsString('tx_golb_listview%5Bdemand%5D%5BcurrentPage%5D=2', $html);

        $html = $this->render(['tx_golb_listview[demand][currentPage]' => 2]);

        self::assertSame(1, substr_count($html, '<article class="golb-post golb-post--teaser'));
        self::assertStringContainsString('Second post', $html);
        self::assertStringContainsString('aria-current="page"', $html);
    }

    #[Test]
    public function listViewAppliesSortingAndFilterSettings(): void
    {
        $this->createPlugin('golb_listview', '2', [
            'sorting' => 'title',
            'sortingDirection' => 'ASC',
            'archived' => 'nonArchived',
            'exclude' => '5',
        ]);

        $html = $this->render();

        self::assertStringContainsString('First post', $html);
        self::assertStringNotContainsString('Second post', $html);
        self::assertStringNotContainsString('Third post', $html);
    }

    #[Test]
    public function listViewFiltersByCategoriesOfThePlugin(): void
    {
        $this->createPlugin('golb_listview', '2', [], [1]);

        $html = $this->render();

        self::assertStringContainsString('Second post', $html);
        self::assertStringNotContainsString('First post', $html);
        self::assertStringNotContainsString('Third post', $html);
    }

    #[Test]
    public function latestViewRespectsLimitAndTopPostsSetting(): void
    {
        $this->createPlugin('golb_latestview', '2', ['limit' => 1]);

        $html = $this->render();
        self::assertSame(1, substr_count($html, '<article class="golb-post golb-post--teaser'));
        self::assertStringContainsString('First post', $html);
        self::assertStringNotContainsString('golb-pagination', $html);

        $this->get(ConnectionPool::class)->getConnectionForTable('tt_content')->delete('tt_content', ['uid' => 100]);
        $this->createPlugin('golb_latestview', '2', ['limit' => 0, 'topPosts' => 'topPosts']);

        $html = $this->render();
        self::assertSame(1, substr_count($html, '<article class="golb-post golb-post--teaser'));
        self::assertStringContainsString('Third post', $html);
    }

    #[Test]
    public function demandArgumentsFilterPostsWhenOverwriteIsAllowed(): void
    {
        $this->createPlugin('golb_listview', '2', ['allowDemandOverwrite' => 1]);

        $html = $this->render(['tx_golb_listview[demand][tag]' => 1]);

        self::assertStringContainsString('First post', $html);
        self::assertStringNotContainsString('Second post', $html);
        self::assertStringNotContainsString('Third post', $html);
    }

    #[Test]
    public function demandArgumentsAreIgnoredWhenOverwriteIsNotAllowed(): void
    {
        $this->createPlugin('golb_listview', '2', ['allowDemandOverwrite' => 0]);

        $html = $this->render(['tx_golb_listview[demand][tag]' => 1]);

        self::assertSame(3, substr_count($html, '<article class="golb-post golb-post--teaser'));
    }

    #[Test]
    public function postPartialRendersMetaData(): void
    {
        $this->createPlugin('golb_latestview', '2', ['limit' => 1]);

        $html = $this->render();

        self::assertStringContainsString('by Zoe', $html);
        self::assertStringContainsString('<span class="golb-term">typo3</span>', $html);
        self::assertStringContainsString('datetime="1970-01-01"', $html);
        self::assertStringContainsString('Read more', $html);
        self::assertStringNotContainsString('golb-flag', $html, 'the first post is neither top post nor archived');
    }

    #[Test]
    public function postPartialRendersFlagsAndLeavesThemOutWhenDisabled(): void
    {
        $this->createPlugin('golb_listview', '2');

        $html = $this->render();

        self::assertStringContainsString('golb-post--top', $html);
        self::assertStringContainsString('<span class="golb-flag golb-flag--top">Top post</span>', $html);
        self::assertStringContainsString('<span class="golb-flag golb-flag--archived">Archived</span>', $html);

        $html = $this->render(['golbSettings' => 'show.flags=0']);

        self::assertStringContainsString('golb-post--top', $html, 'the modifier class stays');
        self::assertStringNotContainsString('golb-flag', $html);
    }

    #[Test]
    public function defaultStylesheetIsAddedOnceAndCanBeDisabled(): void
    {
        $this->createPlugin('golb_listview', '2');
        $this->createPlugin('golb_latestview', '2', [], [], 101);

        $html = $this->render();

        self::assertSame(1, substr_count($html, 'Resources/Public/Css/golb.css'));

        $html = $this->render(['golbSettings' => 'includeCss=0']);

        self::assertStringNotContainsString('golb.css', $html);
        self::assertStringContainsString('<div class="golb">', $html);
    }

    #[Test]
    public function termsLinkToTheFilteredListWhenEnabled(): void
    {
        $this->createPlugin('golb_listview', '2', ['allowDemandOverwrite' => 1]);

        $html = $this->render(['golbSettings' => 'filterLinks.enabled=1']);

        self::assertStringContainsString('class="golb-term golb-term--link"', $html);
        self::assertStringContainsString('tx_golb_listview%5Bdemand%5D%5Btag%5D=2', $html);
        self::assertStringContainsString('tx_golb_listview%5Bdemand%5D%5Bcategory%5D=1', $html);
        self::assertStringNotContainsString('golb-filter', $html, 'no filter is active');

        $html = $this->render(['tx_golb_listview[demand][tag]' => 1, 'golbSettings' => 'filterLinks.enabled=1']);

        self::assertSame(1, substr_count($html, '<article class="golb-post golb-post--teaser'));
        self::assertStringContainsString('Show all posts', $html);
    }
}
