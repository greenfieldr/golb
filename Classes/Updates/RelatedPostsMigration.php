<?php

declare(strict_types=1);

/*
 * This file is part of the TYPO3 extension "golb".
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 3
 * of the License, or any later version.
 */

namespace Greenfieldr\Golb\Updates;

use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Install\Attribute\UpgradeWizard;
use TYPO3\CMS\Install\Updates\DatabaseUpdatedPrerequisite;
use TYPO3\CMS\Install\Updates\UpgradeWizardInterface;

#[UpgradeWizard('relatedPostsMigration')]
final readonly class RelatedPostsMigration implements UpgradeWizardInterface
{
    private const PAGES_TABLE = 'pages';
    private const RELATED_POSTS_FIELD = 'tx_golb_related';
    public function __construct(
        private ConnectionPool $connectionPool,
    ) {}
    public function getTitle(): string
    {
        return 'Migrates pages field for related posts';
    }
    public function getDescription(): string
    {
        return 'The field "tx_golb_related" in table "pages" had a wrong data type for historical reasons. Sets NULL values to 0.';
    }
    public function getPrerequisites(): array
    {
        return [
            DatabaseUpdatedPrerequisite::class,
        ];
    }
    public function updateNecessary(): bool
    {
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable(self::PAGES_TABLE);
        $queryBuilder->getRestrictions()->removeAll();
        $nullValues = $queryBuilder
            ->count('uid')
            ->from(self::PAGES_TABLE)
            ->where($queryBuilder->expr()->isNull(self::RELATED_POSTS_FIELD))
            ->executeQuery()
            ->fetchOne();

        return is_numeric($nullValues) && (int)$nullValues > 0;
    }

    public function executeUpdate(): bool
    {
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable(self::PAGES_TABLE);
        $queryBuilder->getRestrictions()->removeAll();

        $queryBuilder->update(self::PAGES_TABLE)
            ->set(self::RELATED_POSTS_FIELD, 0, true, Connection::PARAM_INT)
            ->where($queryBuilder->expr()->isNull(self::RELATED_POSTS_FIELD))->executeStatement();

        return true;
    }
}
