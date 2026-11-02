<?php

declare(strict_types=1);

/*
 * This file is part of the TYPO3 extension "golb".
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 3
 * of the License, or any later version.
 */

namespace Greenfieldr\Golb\Tests\Functional\Updates;

use Greenfieldr\Golb\Updates\RelatedPostsMigration;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

#[CoversClass(RelatedPostsMigration::class)]
final class RelatedPostsMigrationTest extends FunctionalTestCase
{
    protected array $testExtensionsToLoad = ['greenfieldr/golb'];

    protected array $coreExtensionsToLoad = ['typo3/cms-install'];

    private RelatedPostsMigration $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->importCSVDataSet(__DIR__ . '/../Fixtures/BlogSite.csv');
        $this->subject = $this->get(RelatedPostsMigration::class);
    }

    #[Test]
    public function describesItself(): void
    {
        self::assertNotSame('', $this->subject->getTitle());
        self::assertNotSame('', $this->subject->getDescription());
        self::assertNotSame([], $this->subject->getPrerequisites());
    }

    #[Test]
    public function updateIsNotNecessaryWithoutNullValues(): void
    {
        $connection = $this->get(ConnectionPool::class)->getConnectionForTable('pages');
        $connection->update('pages', ['tx_golb_related' => 0], []);

        self::assertFalse($this->subject->updateNecessary());
        self::assertTrue($this->subject->executeUpdate());
        self::assertFalse($this->subject->updateNecessary());
    }
}
