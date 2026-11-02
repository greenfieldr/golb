<?php

/*
 * This file is part of the TYPO3 extension "golb".
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 3
 * of the License, or any later version.
 */

namespace Greenfieldr\Golb\Domain\Repository;

use Greenfieldr\Golb\Domain\Model\Tag;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\Repository;

/**
 * The repository for tags
 */
class TagRepository extends Repository
{
    protected $defaultQuerySettings;

    protected $defaultOrderings = [
        'title' => QueryInterface::ORDER_ASCENDING,
        'uid' => QueryInterface::ORDER_ASCENDING,
    ];

    public function initializeObject(): void
    {
        $this->defaultQuerySettings = GeneralUtility::makeInstance(Typo3QuerySettings::class);
        $this->defaultQuerySettings->setRespectStoragePage(false);
        $this->defaultQuerySettings->setIgnoreEnableFields(false);
        $this->defaultQuerySettings->setRespectSysLanguage(true);
        $this->setDefaultQuerySettings($this->defaultQuerySettings);
    }

    public function findAllWithAtLeastOneBlogPost(): array
    {
        $query = $this->createQuery();

        $querySettings = $query->getQuerySettings();
        $querySettings->setRespectStoragePage(false);
        $query->setQuerySettings($querySettings);

        $result = $query->execute()->toArray();

        /** @var Tag $tag */
        foreach ($result as $key => $tag) {
            if ($tag->getPages() === null) {
                unset($result[$key]);
            } elseif ($tag->getPages()->count() === 0) {
                unset($result[$key]);
            }
        }

        return $result;
    }
}
