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

use TYPO3\CMS\Extbase\Annotation as Extbase;
use TYPO3\CMS\Extbase\Annotation\ORM\Lazy;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\Generic\LazyLoadingProxy;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/**
 * A system category (table sys_category) including its sub categories.
 */
class Category extends AbstractEntity
{
    #[Extbase\Validate(['validator' => 'NotEmpty'])]
    protected string $title = '';

    protected string $description = '';

    #[Lazy]
    protected Category|LazyLoadingProxy|null $parent = null;

    /**
     * @var ObjectStorage<Category>
     */
    #[Lazy]
    protected ObjectStorage $subCategories;

    public function __construct()
    {
        $this->initializeObject();
    }

    public function initializeObject(): void
    {
        $this->subCategories = new ObjectStorage();
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

    public function getParent(): ?Category
    {
        if ($this->parent instanceof LazyLoadingProxy) {
            $this->parent = $this->parent->_loadRealInstance();
        }

        return $this->parent;
    }

    public function setParent(?Category $parent): void
    {
        $this->parent = $parent;
    }

    public function addSubCategory(Category $category): void
    {
        $this->subCategories->attach($category);
    }

    public function removeSubCategory(Category $category): void
    {
        $this->subCategories->detach($category);
    }

    /**
     * @return ObjectStorage<Category>
     */
    public function getSubCategories(): ObjectStorage
    {
        return $this->subCategories;
    }

    /**
     * @param ObjectStorage<Category> $categories
     */
    public function setSubCategories(ObjectStorage $categories): void
    {
        $this->subCategories = $categories;
    }
}
