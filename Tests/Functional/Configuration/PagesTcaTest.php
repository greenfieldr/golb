<?php

declare(strict_types=1);

/*
 * This file is part of the TYPO3 extension "golb".
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 3
 * of the License, or any later version.
 */

namespace Greenfieldr\Golb\Tests\Functional\Configuration;

use Greenfieldr\Golb\Constants;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\DataHandling\DataHandler;
use TYPO3\CMS\Core\DataHandling\PageDoktypeRegistry;
use TYPO3\CMS\Core\Domain\Repository\PageRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

#[CoversNothing]
final class PagesTcaTest extends FunctionalTestCase
{
    protected array $testExtensionsToLoad = ['greenfieldr/golb'];

    protected array $coreExtensionsToLoad = ['typo3/cms-install'];

    protected function setUp(): void
    {
        parent::setUp();
        $this->importCSVDataSet(__DIR__ . '/../Fixtures/BlogSite.csv');
        $this->importCSVDataSet(__DIR__ . '/../Fixtures/BackendAdmin.csv');
        $this->setUpBackendUser(1);
    }

    #[Test]
    public function contentIsAllowedOnStandardAndBlogPostPages(): void
    {
        $registry = $this->get(PageDoktypeRegistry::class);

        self::assertTrue($registry->isRecordTypeAllowedForDoktype('tt_content', PageRepository::DOKTYPE_DEFAULT));
        self::assertTrue($registry->isRecordTypeAllowedForDoktype('tt_content', Constants::BLOG_POST_DOKTYPE));
    }

    #[Test]
    public function contentCanBeCreatedOnABlogPostPageThroughTheDataHandler(): void
    {
        $dataHandler = GeneralUtility::makeInstance(DataHandler::class);
        $dataHandler->start(
            [
                'tt_content' => [
                    'NEW1' => ['pid' => 3, 'CType' => 'text', 'header' => 'On blog post'],
                    'NEW2' => ['pid' => 2, 'CType' => 'text', 'header' => 'On standard page'],
                ],
            ],
            []
        );
        $dataHandler->process_datamap();

        self::assertSame([], $dataHandler->errorLog);
        $headers = array_column(
            $this->get(ConnectionPool::class)
                ->getConnectionForTable('tt_content')
                ->select(['header'], 'tt_content')
                ->fetchAllAssociative(),
            'header'
        );
        sort($headers);
        self::assertSame(['On blog post', 'On standard page'], $headers);
    }
}
