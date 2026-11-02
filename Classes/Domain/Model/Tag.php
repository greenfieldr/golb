<?php

declare(strict_types=1);

/*
 * This file is part of the TYPO3 extension "golb".
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 3
 * of the License, or any later version.
 */

namespace Greenfieldr\Golb\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/**
 * A tag that can be assigned to blog posts (table tx_golb_domain_model_tag).
 */
class Tag extends AbstractEntity
{
    protected string $title = '';

    protected string $description = '';

    protected string $slug = '';

    /**
     * @var ObjectStorage<Page>
     */
    protected ObjectStorage $pages;

    public function __construct()
    {
        $this->initializeObject();
    }

    public function initializeObject(): void
    {
        $this->pages = new ObjectStorage();
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): void
    {
        $this->slug = $slug;
    }

    /**
     * @return ObjectStorage<Page>
     */
    public function getPages(): ObjectStorage
    {
        return $this->pages;
    }

    /**
     * @param ObjectStorage<Page> $pages
     */
    public function setPages(ObjectStorage $pages): void
    {
        $this->pages = $pages;
    }

    public function addPage(Page $page): void
    {
        $this->pages->attach($page);
    }

    public function removePage(Page $page): void
    {
        $this->pages->detach($page);
    }
}
