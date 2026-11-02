<?php

declare(strict_types=1);

/*
 * This file is part of the TYPO3 extension "golb".
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 3
 * of the License, or any later version.
 */

namespace Greenfieldr\Golb\Domain\Repository;

use Greenfieldr\Golb\Domain\Model\Category;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Extbase\Persistence\Generic\Mapper\DataMapper;
use TYPO3\CMS\Extbase\Persistence\Repository;

/**
 * @extends Repository<Category>
 */
class CategoryRepository extends Repository
{
    public function __construct(
        private readonly ConnectionPool $connectionPool,
        private readonly DataMapper $dataMapper
    ) {
        parent::__construct();
    }

    /**
     * Returns the categories assigned to a record, by default the categories
     * selected in a content element (plugin).
     *
     * @return Category[]
     */
    public function findByRelation(int $id, string $tableName = 'tt_content', string $fieldName = 'categories'): array
    {
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable('sys_category');
        $rows = $queryBuilder
            ->select('sys_category.*')
            ->from('sys_category')
            ->join(
                'sys_category',
                'sys_category_record_mm',
                'mm',
                $queryBuilder->expr()->eq('mm.uid_local', $queryBuilder->quoteIdentifier('sys_category.uid'))
            )
            ->where(
                $queryBuilder->expr()->eq('mm.tablenames', $queryBuilder->createNamedParameter($tableName)),
                $queryBuilder->expr()->eq('mm.fieldname', $queryBuilder->createNamedParameter($fieldName)),
                $queryBuilder->expr()->eq('mm.uid_foreign', $queryBuilder->createNamedParameter($id, Connection::PARAM_INT))
            )
            ->orderBy('mm.sorting_foreign')
            ->executeQuery()
            ->fetchAllAssociative();

        /** @var Category[] $categories */
        $categories = $this->dataMapper->map(Category::class, $rows);

        return $categories;
    }
}
