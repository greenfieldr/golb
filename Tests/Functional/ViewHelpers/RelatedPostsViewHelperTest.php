<?php

declare(strict_types=1);

/*
 * This file is part of the TYPO3 extension "golb".
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 3
 * of the License, or any later version.
 */

namespace Greenfieldr\Golb\Tests\Functional\ViewHelpers;

use Greenfieldr\Golb\DataProcessing\MetaInformationProcessor;
use Greenfieldr\Golb\ViewHelpers\RelatedPostsViewHelper;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\TestingFramework\Core\Functional\Framework\Frontend\InternalRequest;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

/**
 * Renders a blog post page with the data processor and the related posts
 * ViewHelper through the frontend.
 */
#[CoversClass(RelatedPostsViewHelper::class)]
#[CoversClass(MetaInformationProcessor::class)]
final class RelatedPostsViewHelperTest extends FunctionalTestCase
{
    protected array $testExtensionsToLoad = ['greenfieldr/golb'];

    protected array $coreExtensionsToLoad = ['typo3/cms-install'];

    protected array $pathsToLinkInTestInstance = [
        'typo3conf/ext/golb/Tests/Functional/Controller/Fixtures/Sites/' => 'typo3conf/sites',
    ];

    protected function setUp(): void
    {
        parent::setUp();

        $this->importCSVDataSet(__DIR__ . '/../Fixtures/BlogSite.csv');
        $this->setUpFrontendRootPage(1, [
            'setup' => [
                'EXT:golb/Tests/Functional/ViewHelpers/Fixtures/Rendering.typoscript',
            ],
        ]);
    }

    private function render(int $pageId): string
    {
        $request = (new InternalRequest())->withPageId($pageId);

        return (string)$this->executeFrontendSubRequest($request)->getBody();
    }

    #[Test]
    public function dataProcessorProvidesTheCurrentPost(): void
    {
        $html = $this->render(3);

        self::assertStringContainsString('[post:First post:41]', $html);
    }

    #[Test]
    public function relatedPostsAreRankedBySharedTagsAndExcludeTheCurrentPost(): void
    {
        $html = $this->render(3);

        self::assertStringContainsString('[related:Second post:1]', $html);
        self::assertStringNotContainsString('[related:First post', $html);
        self::assertStringNotContainsString('[related:Third post', $html);
    }

    #[Test]
    public function postWithoutMatchingTagsHasNoRelatedPosts(): void
    {
        $html = $this->render(5);

        self::assertStringContainsString('[post:Third post:41]', $html);
        self::assertStringNotContainsString('[related:', $html);
    }

    #[Test]
    public function regularPagesAreProvidedAsPageObjectsToo(): void
    {
        $html = $this->render(2);

        self::assertStringContainsString('[post:Blog:1]', $html);
    }
}
