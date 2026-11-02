<?php

declare(strict_types=1);

namespace Greenfieldr\Golb\Updates;

use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Install\Attribute\UpgradeWizard;
use TYPO3\CMS\Install\Updates\UpgradeWizardInterface;

#[UpgradeWizard('relatedPostsMigration')]
final readonly class RelatedPostsMigration implements UpgradeWizardInterface
{
    protected const PAGES_TABLE = 'pages';
    protected const RELATED_POSTS_FIELD = 'tx_golb_related';
    public function __construct(
        private ConnectionPool $connectionPool,
    ) {}
    public function getTitle(): string
    {
        return 'Migrates pages field for related posts';
    }
    public function getDescription(): string
    {
        return 'The relational field in pages table had a wrong data type due to historical reasons. Lets migrate it.';
    }
    public function getPrerequisites(): array
    {
        return [];
    }
    public function updateNecessary(): bool
    {
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable(self::PAGES_TABLE);
        $queryBuilder->getRestrictions()->removeAll();

        return ($queryBuilder->select(self::RELATED_POSTS_FIELD)->from(self::PAGES_TABLE)
            ->where(
                $queryBuilder->expr()->isNull(self::RELATED_POSTS_FIELD)
            )->executeQuery()->rowCount() > 0);
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
