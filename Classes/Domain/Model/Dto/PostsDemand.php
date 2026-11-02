<?php

declare(strict_types=1);

/*
 * This file is part of the TYPO3 extension "golb".
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 3
 * of the License, or any later version.
 */

namespace Greenfieldr\Golb\Domain\Model\Dto;

use Greenfieldr\Golb\Domain\Model\Category;

/**
 * Describes which blog posts should be fetched: filters, sorting and
 * pagination. A demand object is built from the plugin settings and may be
 * overridden by request arguments if the plugin allows it.
 */
class PostsDemand
{
    public const LIMIT_DEFAULT = 10;
    public const ITEMS_PER_PAGE_DEFAULT = 10;
    public const OFFSET_DEFAULT = 0;
    public const ORDER_DEFAULT = 'date';
    public const ORDER_DIRECTION_DEFAULT = 'DESC';
    public const ARCHIVED_DEFAULT = false;
    public const NON_ARCHIVED_DEFAULT = false;
    public const TOP_POST_DEFAULT = false;
    public const NON_TOP_POST_DEFAULT = false;

    public const ORDER_DATE = 'date';
    public const ORDER_CREATION_DATE = 'crdate';
    public const ORDER_MODIFICATION_DATE = 'tstamp';
    public const ORDER_TITLE = 'title';
    public const ORDER_AUTHOR = 'author';

    protected ?bool $archived = null;

    protected ?bool $nonArchived = null;

    protected ?bool $topPost = null;

    protected ?bool $nonTopPost = null;

    /**
     * @var Category[]
     */
    protected array $categories = [];

    protected ?Category $category = null;

    /**
     * @var int[]
     */
    protected array $tags = [];

    protected ?int $tag = null;

    protected ?int $limit = null;

    protected ?int $offset = null;

    /**
     * @var int[]
     */
    protected array $excluded = [];

    protected ?string $order = null;

    protected ?string $orderDirection = null;

    protected int $currentPage = 1;

    protected int $itemsPerPage = 0;

    public function isArchived(): bool
    {
        return $this->archived ?? self::ARCHIVED_DEFAULT;
    }

    public function isArchivedSet(): bool
    {
        return $this->archived !== null;
    }

    public function setArchived(bool $archived): static
    {
        $this->archived = $archived;
        return $this;
    }

    public function isNonArchived(): bool
    {
        return $this->nonArchived ?? self::NON_ARCHIVED_DEFAULT;
    }

    public function isNonArchivedSet(): bool
    {
        return $this->nonArchived !== null;
    }

    public function setNonArchived(bool $nonArchived): static
    {
        $this->nonArchived = $nonArchived;
        return $this;
    }

    public function isTopPost(): bool
    {
        return $this->topPost ?? self::TOP_POST_DEFAULT;
    }

    public function isTopPostSet(): bool
    {
        return $this->topPost !== null;
    }

    public function setTopPost(bool $topPost): static
    {
        $this->topPost = $topPost;
        return $this;
    }

    public function isNonTopPost(): bool
    {
        return $this->nonTopPost ?? self::NON_TOP_POST_DEFAULT;
    }

    public function isNonTopPostSet(): bool
    {
        return $this->nonTopPost !== null;
    }

    public function setNonTopPost(bool $nonTopPost): static
    {
        $this->nonTopPost = $nonTopPost;
        return $this;
    }

    /**
     * @return Category[]
     */
    public function getCategories(): array
    {
        return $this->categories;
    }

    /**
     * @param Category[] $categories
     */
    public function setCategories(array $categories): static
    {
        $this->categories = $categories;
        return $this;
    }

    public function hasCategories(): bool
    {
        return $this->categories !== [];
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): static
    {
        $this->category = $category;
        return $this;
    }

    /**
     * Returns the tag uids to filter by, including the single tag if set.
     *
     * @return int[]
     */
    public function getTags(): array
    {
        $tags = $this->tags;
        if ($this->tag !== null && !in_array($this->tag, $tags, true)) {
            $tags[] = $this->tag;
        }

        return $tags;
    }

    /**
     * Sets a single tag uid, e.g. for speaking URLs.
     */
    public function setTag(?int $tag): static
    {
        $this->tag = $tag;
        return $this;
    }

    public function getTag(): ?int
    {
        return $this->tag;
    }

    /**
     * @param int[] $tags
     */
    public function setTags(array $tags): static
    {
        $this->tags = array_map(intval(...), $tags);
        return $this;
    }

    public function hasTags(): bool
    {
        return $this->tags !== [] || $this->tag !== null;
    }

    public function getLimit(): int
    {
        return $this->limit ?? self::LIMIT_DEFAULT;
    }

    public function setLimit(int $limit): static
    {
        $this->limit = $limit;
        return $this;
    }

    public function hasLimit(): bool
    {
        return $this->limit !== null;
    }

    public function getOffset(): int
    {
        return $this->offset ?? self::OFFSET_DEFAULT;
    }

    public function setOffset(int $offset): static
    {
        $this->offset = $offset;
        return $this;
    }

    public function hasOffset(): bool
    {
        return $this->offset !== null;
    }

    /**
     * @return int[]
     */
    public function getExcluded(): array
    {
        return $this->excluded;
    }

    /**
     * @param int[] $excluded
     */
    public function setExcluded(array $excluded): static
    {
        $this->excluded = array_map(intval(...), $excluded);
        return $this;
    }

    public function hasExcluded(): bool
    {
        return $this->excluded !== [];
    }

    public function getOrder(): string
    {
        return $this->order ?? self::ORDER_DEFAULT;
    }

    public function setOrder(string $order): static
    {
        $this->order = $order;
        return $this;
    }

    public function hasOrder(): bool
    {
        return $this->order !== null;
    }

    public function getOrderDirection(): string
    {
        return $this->orderDirection ?? self::ORDER_DIRECTION_DEFAULT;
    }

    public function hasOrderDirection(): bool
    {
        return $this->orderDirection !== null;
    }

    public function setOrderDirection(string $orderDirection): static
    {
        $this->orderDirection = strtoupper($orderDirection);
        return $this;
    }

    public function getCurrentPage(): int
    {
        return $this->currentPage;
    }

    public function setCurrentPage(int $currentPage): static
    {
        $this->currentPage = max(1, $currentPage);
        return $this;
    }

    public function hasItemsPerPage(): bool
    {
        return $this->itemsPerPage > 0;
    }

    public function getItemsPerPage(): int
    {
        return $this->hasItemsPerPage() ? $this->itemsPerPage : self::ITEMS_PER_PAGE_DEFAULT;
    }

    public function setItemsPerPage(int $itemsPerPage): static
    {
        $this->itemsPerPage = $itemsPerPage;
        return $this;
    }
}
