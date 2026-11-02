<?php

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
 * Tag
 */
class Tag extends AbstractEntity
{
    /**
     * @var string
     */
    protected string $title = '';

    /**
     * @var string
     */
    protected string $description = '';

    /**
     * @var string
     */
    protected string $slug = '';

    /**
     * @var ?ObjectStorage<Page>
     */
    protected ?ObjectStorage $pages = null;

    /**
     * Tag constructor.
     */
    public function __construct()
    {
        $this->setPages(new ObjectStorage());
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getSlug(): string
    {
        return $this->slug;
    }

    /**
     * @param string $slug
     */
    public function setSlug(string $slug): void
    {
        $this->slug = $slug;
    }
    /**
     * @return ?ObjectStorage
     */
    public function getPages(): ?ObjectStorage
    {
        return $this->pages;
    }

    /**
     * @param ObjectStorage $pages
     */
    public function setPages(ObjectStorage $pages): void
    {
        $this->pages = $pages;
    }

    /**
     * @param Page $page
     */
    public function addPage(Page $page): void
    {
        $this->pages->attach($page);
    }

    /**
     * @param Page $page
     */
    public function removePage(Page $page): void
    {
        $this->removePage($page);
    }
}
