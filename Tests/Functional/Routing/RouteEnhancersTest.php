<?php

declare(strict_types=1);

/*
 * This file is part of the TYPO3 extension "golb".
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 3
 * of the License, or any later version.
 */

namespace Greenfieldr\Golb\Tests\Functional\Routing;

use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\Test;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\TestingFramework\Core\Functional\Framework\Frontend\InternalRequest;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

/**
 * Imports the shipped route enhancer configuration into the site of the test
 * instance and checks that the List view plugin generates and resolves
 * speaking URLs. The plugin sits on the root page (slug "/") and lists two
 * posts per page: First post (tags php and typo3), Third post and Second
 * post (tag typo3, category News) in this order.
 */
#[CoversNothing]
final class RouteEnhancersTest extends FunctionalTestCase
{
    protected array $testExtensionsToLoad = ['greenfieldr/golb'];

    protected array $coreExtensionsToLoad = ['typo3/cms-install', 'typo3/cms-fluid-styled-content'];

    protected array $pathsToLinkInTestInstance = [
        'typo3conf/ext/golb/Tests/Functional/Routing/Fixtures/Sites/' => 'typo3conf/sites',
    ];

    protected function setUp(): void
    {
        parent::setUp();

        $this->importCSVDataSet(__DIR__ . '/../Fixtures/BlogSite.csv');
        $this->get(ConnectionPool::class)->getConnectionForTable('tt_content')->insert('tt_content', [
            'uid' => 100,
            'pid' => 1,
            'colPos' => 0,
            'sorting' => 1,
            'CType' => 'golb_listview',
            'header' => 'List',
            'pages' => '2',
            'pi_flexform' => '<?xml version="1.0" encoding="utf-8" standalone="yes" ?><T3FlexForms><data>'
                . '<sheet index="sDEF"><language index="lDEF">'
                . '<field index="settings.itemsPerPage"><value index="vDEF">2</value></field>'
                . '<field index="settings.allowDemandOverwrite"><value index="vDEF">1</value></field>'
                . '</language></sheet></data></T3FlexForms>',
        ]);
        $this->setUpFrontendRootPage(1, [
            'constants' => [
                'EXT:fluid_styled_content/Configuration/TypoScript/constants.typoscript',
                'EXT:golb/Configuration/TypoScript/constants.typoscript',
            ],
            'setup' => [
                'EXT:fluid_styled_content/Configuration/TypoScript/setup.typoscript',
                'EXT:golb/Configuration/TypoScript/setup.typoscript',
                'EXT:golb/Tests/Functional/Routing/Fixtures/Rendering.typoscript',
            ],
        ]);
    }

    private function request(string $path): ResponseInterface
    {
        return $this->executeFrontendSubRequest(new InternalRequest('http://localhost' . $path));
    }

    /**
     * @return string[] titles of the listed posts in order
     */
    private function listedPosts(string $html): array
    {
        preg_match_all('/<h3 class="golb-post__title">\s*<a[^>]*>([^<]+)<\/a>/', $html, $matches);

        return $matches[1];
    }

    #[Test]
    public function paginationAndFilterLinksAreSpeakingUrls(): void
    {
        $html = (string)$this->request('/')->getBody();

        self::assertStringContainsString('href="/page/2"', $html);
        self::assertStringContainsString('href="/tag/typo3"', $html);
        self::assertStringContainsString('href="/tag/php"', $html);
        self::assertStringNotContainsString('tx_golb_listview', $html);
        self::assertStringNotContainsString('cHash', $html);
    }

    #[Test]
    public function categoryLinkIsSpeakingUrl(): void
    {
        $html = (string)$this->request('/page/2')->getBody();

        self::assertStringContainsString('href="/category/News-1"', $html);
        self::assertStringNotContainsString('cHash', $html);
    }

    #[Test]
    public function paginationRouteIsResolved(): void
    {
        $response = $this->request('/page/2');

        self::assertSame(200, $response->getStatusCode());
        self::assertSame(['Second post'], $this->listedPosts((string)$response->getBody()));
    }

    #[Test]
    public function tagRouteIsResolved(): void
    {
        self::assertSame(['First post', 'Second post'], $this->listedPosts((string)$this->request('/tag/typo3')->getBody()));
        self::assertSame(['First post'], $this->listedPosts((string)$this->request('/tag/php')->getBody()));
    }

    #[Test]
    public function tagRouteWithPaginationIsResolved(): void
    {
        $html = (string)$this->request('/tag/typo3/page/1')->getBody();

        self::assertSame(['First post', 'Second post'], $this->listedPosts($html));
    }

    #[Test]
    public function categoryRouteIsResolved(): void
    {
        $html = (string)$this->request('/category/News-1')->getBody();

        self::assertSame(['Second post'], $this->listedPosts($html));
    }

    #[Test]
    public function unknownTagAnswersWithNotFound(): void
    {
        self::assertSame(404, $this->request('/tag/no-such-tag')->getStatusCode());
    }
}
