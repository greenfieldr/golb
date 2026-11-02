<?php

/*
 * This file is part of the TYPO3 extension "golb".
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 3
 * of the License, or any later version.
 */

namespace Greenfieldr\Golb\Domain\Repository;

use TYPO3\CMS\Extbase\Persistence\Generic\Query;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use TYPO3\CMS\Extbase\Persistence\Repository;

/**
 * The repository for categories
 */
class CategoryRepository extends Repository
{
    /**
     * @param int $id
     * @param string $tableName
     * @param string $fieldName
     * @return ?QueryResultInterface
     */
    public function findByRelation(int $id, string $tableName = 'tt_content', string $fieldName = 'categories'): ?QueryResultInterface
    {
        /** @var Query $query */
        $query = $this->createQuery();
        $query->statement(
            'SELECT * '
            . 'FROM sys_category as cat, sys_category_record_mm as mm '
            . 'WHERE mm.tablenames = "' . $tableName . '" '
            . 'AND mm.uid_foreign = ' . $id . ' '
            . 'AND mm.fieldname = "' . $fieldName . '" '
            . 'AND cat.uid = mm.uid_local'
        );

        return $query->execute();
    }
}
