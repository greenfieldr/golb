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

use TYPO3\CMS\Extbase\Attribute\ORM\Lazy;
use TYPO3\CMS\Extbase\Domain\Model\FileReference;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\Generic\LazyLoadingProxy;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/**
 * A page record (table pages). Blog posts are pages with the golb doktype.
 */
class Page extends AbstractEntity
{
    protected string $abstract = '';

    protected string $author = '';

    protected string $authorEmail = '';

    #[Lazy]
    protected FileReference|LazyLoadingProxy|null $authorImage = null;

    protected string $backendLayout = '';

    protected string $backendLayoutNextLevel = '';

    protected string $cacheTags = '';

    protected ?int $cacheTimeout = null;

    /**
     * @var ObjectStorage<Category>
     */
    #[Lazy]
    protected ObjectStorage $categories;

    protected ?int $contentFromPid = null;

    /**
     * Maps on field "crdate".
     */
    protected ?int $creationDate = null;

    protected bool $deleted = false;

    protected string $description = '';

    protected ?int $doktype = null;

    protected ?int $endtime = null;

    /**
     * Maps on field "extendToSubpages".
     */
    protected bool $extendToSubpages = false;

    protected string $feGroup = '';

    protected bool $hidden = false;

    protected bool $navHide = false;

    protected string $keywords = '';

    /**
     * Maps on field "lastUpdated".
     */
    protected ?int $lastUpdated = null;

    protected ?int $layout = null;

    /**
     * @var ObjectStorage<FileReference>
     */
    #[Lazy]
    protected ObjectStorage $media;

    protected ?int $mountPid = null;

    protected ?int $mountPidOl = null;

    protected string $navTitle = '';

    /**
     * Maps on field "newUntil".
     */
    protected ?int $newUntil = null;

    protected bool $noSearch = false;

    /**
     * Related blog posts, maps on field "tx_golb_related".
     *
     * @var ObjectStorage<Page>
     */
    #[Lazy]
    protected ObjectStorage $relatedPages;

    protected ?int $shortcut = null;

    protected ?int $shortcutMode = null;

    protected bool $isSiteroot = false;

    protected ?int $sorting = null;

    protected ?int $starttime = null;

    /**
     * Direct sub pages, maps on field "tx_golb_subpages".
     *
     * @var ObjectStorage<Page>
     */
    #[Lazy]
    protected ObjectStorage $subpages;

    protected string $subtitle = '';

    protected string $target = '';

    protected ?int $tstamp = null;

    protected string $title = '';

    protected string $url = '';

    /**
     * Maps on field "tx_golb_publish_date".
     */
    protected ?int $publishDate = null;

    /**
     * Content elements on this page, maps on field "tx_golb_content_elements".
     *
     * @var ObjectStorage<ContentElement>
     */
    #[Lazy]
    protected ObjectStorage $contentElements;

    /**
     * Maps on field "tx_golb_tags".
     *
     * @var ObjectStorage<Tag>
     */
    #[Lazy]
    protected ObjectStorage $tags;

    /**
     * Maps on field "tx_golb_archived".
     */
    protected bool $archived = false;

    /**
     * Maps on field "tx_golb_top_post".
     */
    protected bool $topPost = false;

    public function __construct()
    {
        $this->initializeObject();
    }

    public function initializeObject(): void
    {
        $this->categories = new ObjectStorage();
        $this->relatedPages = new ObjectStorage();
        $this->subpages = new ObjectStorage();
        $this->contentElements = new ObjectStorage();
        $this->media = new ObjectStorage();
        $this->tags = new ObjectStorage();
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getSubtitle(): string
    {
        return $this->subtitle;
    }

    public function setSubtitle(string $subtitle): void
    {
        $this->subtitle = $subtitle;
    }

    public function getNavTitle(): string
    {
        return $this->navTitle;
    }

    public function setNavTitle(string $navTitle): void
    {
        $this->navTitle = $navTitle;
    }

    /**
     * Returns the navigation title, falling back to the page title.
     */
    public function getNavigationTitle(): string
    {
        return $this->navTitle !== '' ? $this->navTitle : $this->title;
    }

    public function setNavigationTitle(string $navigationTitle): void
    {
        $this->navTitle = $navigationTitle;
    }

    public function getAbstract(): string
    {
        return $this->abstract;
    }

    public function setAbstract(string $abstract): void
    {
        $this->abstract = $abstract;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getKeywords(): string
    {
        return $this->keywords;
    }

    public function setKeywords(string $keywords): void
    {
        $this->keywords = $keywords;
    }

    public function getAuthor(): string
    {
        return $this->author;
    }

    public function setAuthor(string $author): void
    {
        $this->author = $author;
    }

    public function getAuthorName(): string
    {
        return $this->author;
    }

    public function setAuthorName(string $authorName): void
    {
        $this->author = $authorName;
    }

    public function getAuthorEmail(): string
    {
        return $this->authorEmail;
    }

    public function setAuthorEmail(string $authorEmail): void
    {
        $this->authorEmail = $authorEmail;
    }

    public function getAuthorImage(): ?FileReference
    {
        if ($this->authorImage instanceof LazyLoadingProxy) {
            $authorImage = $this->authorImage->_loadRealInstance();
            $this->authorImage = $authorImage instanceof FileReference ? $authorImage : null;
        }

        return $this->authorImage;
    }

    public function setAuthorImage(?FileReference $authorImage): void
    {
        $this->authorImage = $authorImage;
    }

    public function getBackendLayout(): string
    {
        return $this->backendLayout;
    }

    public function setBackendLayout(string $backendLayout): void
    {
        $this->backendLayout = $backendLayout;
    }

    public function getBackendLayoutNextLevel(): string
    {
        return $this->backendLayoutNextLevel;
    }

    public function setBackendLayoutNextLevel(string $backendLayoutNextLevel): void
    {
        $this->backendLayoutNextLevel = $backendLayoutNextLevel;
    }

    public function getCacheTags(): string
    {
        return $this->cacheTags;
    }

    public function setCacheTags(string $cacheTags): void
    {
        $this->cacheTags = $cacheTags;
    }

    public function getCacheTimeout(): ?int
    {
        return $this->cacheTimeout;
    }

    public function setCacheTimeout(?int $cacheTimeout): void
    {
        $this->cacheTimeout = $cacheTimeout;
    }

    /**
     * @return ObjectStorage<Category>
     */
    public function getCategories(): ObjectStorage
    {
        return $this->categories;
    }

    /**
     * @param ObjectStorage<Category> $categories
     */
    public function setCategories(ObjectStorage $categories): void
    {
        $this->categories = $categories;
    }

    public function addCategory(Category $category): void
    {
        $this->categories->attach($category);
    }

    public function removeCategory(Category $category): void
    {
        $this->categories->detach($category);
    }

    public function getContentFromPid(): ?int
    {
        return $this->contentFromPid;
    }

    public function setContentFromPid(?int $contentFromPid): void
    {
        $this->contentFromPid = $contentFromPid;
    }

    public function getCreationDate(): ?int
    {
        return $this->creationDate;
    }

    public function setCreationDate(?int $creationDate): void
    {
        $this->creationDate = $creationDate;
    }

    public function isDeleted(): bool
    {
        return $this->deleted;
    }

    public function getDoktype(): ?int
    {
        return $this->doktype;
    }

    public function setDoktype(int $doktype): void
    {
        $this->doktype = $doktype;
    }

    public function getDocumentType(): ?int
    {
        return $this->doktype;
    }

    public function setDocumentType(int $documentType): void
    {
        $this->doktype = $documentType;
    }

    public function getStartTime(): ?int
    {
        return $this->starttime;
    }

    public function setStartTime(?int $startTime): void
    {
        $this->starttime = $startTime;
    }

    public function getEndTime(): ?int
    {
        return $this->endtime;
    }

    public function setEndTime(?int $endTime): void
    {
        $this->endtime = $endTime;
    }

    public function isExtendToSubpages(): bool
    {
        return $this->extendToSubpages;
    }

    public function setExtendToSubpages(bool $extendToSubpages): void
    {
        $this->extendToSubpages = $extendToSubpages;
    }

    public function getFeGroup(): string
    {
        return $this->feGroup;
    }

    public function setFeGroup(string $feGroup): void
    {
        $this->feGroup = $feGroup;
    }

    public function isHidden(): bool
    {
        return $this->hidden;
    }

    public function isNavHide(): bool
    {
        return $this->navHide;
    }

    public function setNavHide(bool $navHide): void
    {
        $this->navHide = $navHide;
    }

    public function getLastUpdated(): ?int
    {
        return $this->lastUpdated;
    }

    public function setLastUpdated(?int $lastUpdated): void
    {
        $this->lastUpdated = $lastUpdated;
    }

    public function getLayout(): ?int
    {
        return $this->layout;
    }

    public function setLayout(?int $layout): void
    {
        $this->layout = $layout;
    }

    /**
     * @return ObjectStorage<FileReference>
     */
    public function getMedia(): ObjectStorage
    {
        return $this->media;
    }

    /**
     * @param ObjectStorage<FileReference> $media
     */
    public function setMedia(ObjectStorage $media): void
    {
        $this->media = $media;
    }

    /**
     * Returns the first file of the "Media" field, e.g. as teaser image, or
     * null if there is none.
     */
    public function getFirstMedia(): ?FileReference
    {
        foreach ($this->media as $file) {
            return $file;
        }

        return null;
    }

    public function getMountPid(): ?int
    {
        return $this->mountPid;
    }

    public function setMountPid(?int $mountPid): void
    {
        $this->mountPid = $mountPid;
    }

    public function getMountPidOverlay(): ?int
    {
        return $this->mountPidOl;
    }

    public function setMountPidOverlay(?int $mountPidOverlay): void
    {
        $this->mountPidOl = $mountPidOverlay;
    }

    public function getNewUntil(): ?int
    {
        return $this->newUntil;
    }

    public function setNewUntil(?int $newUntil): void
    {
        $this->newUntil = $newUntil;
    }

    public function isNoSearch(): bool
    {
        return $this->noSearch;
    }

    public function setNoSearch(bool $noSearch): void
    {
        $this->noSearch = $noSearch;
    }

    /**
     * @return ObjectStorage<Page>
     */
    public function getRelatedPages(): ObjectStorage
    {
        return $this->relatedPages;
    }

    /**
     * @param ObjectStorage<Page> $relatedPages
     */
    public function setRelatedPages(ObjectStorage $relatedPages): void
    {
        $this->relatedPages = $relatedPages;
    }

    public function addRelatedPage(Page $relatedPage): void
    {
        $this->relatedPages->attach($relatedPage);
    }

    public function removeRelatedPage(Page $relatedPage): void
    {
        $this->relatedPages->detach($relatedPage);
    }

    public function getShortcut(): ?int
    {
        return $this->shortcut;
    }

    public function setShortcut(?int $shortcut): void
    {
        $this->shortcut = $shortcut;
    }

    public function getShortcutMode(): ?int
    {
        return $this->shortcutMode;
    }

    public function setShortcutMode(?int $shortcutMode): void
    {
        $this->shortcutMode = $shortcutMode;
    }

    public function isSiteRoot(): bool
    {
        return $this->isSiteroot;
    }

    public function setSiteRoot(bool $siteRoot): void
    {
        $this->isSiteroot = $siteRoot;
    }

    public function getSorting(): ?int
    {
        return $this->sorting;
    }

    public function setSorting(?int $sorting): void
    {
        $this->sorting = $sorting;
    }

    /**
     * @return ObjectStorage<Page>
     */
    public function getSubpages(): ObjectStorage
    {
        return $this->subpages;
    }

    /**
     * @param ObjectStorage<Page> $subpages
     */
    public function setSubpages(ObjectStorage $subpages): void
    {
        $this->subpages = $subpages;
    }

    public function addSubpage(Page $subpage): void
    {
        $this->subpages->attach($subpage);
    }

    public function removeSubpage(Page $subpage): void
    {
        $this->subpages->detach($subpage);
    }

    public function getTarget(): string
    {
        return $this->target;
    }

    public function setTarget(string $target): void
    {
        $this->target = $target;
    }

    public function getTstamp(): ?int
    {
        return $this->tstamp;
    }

    public function setTstamp(?int $tstamp): void
    {
        $this->tstamp = $tstamp;
    }

    public function getModificationDate(): ?int
    {
        return $this->tstamp;
    }

    public function setModificationDate(?int $timestamp): void
    {
        $this->tstamp = $timestamp;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function setUrl(string $url): void
    {
        $this->url = $url;
    }

    public function getPublishDate(): ?int
    {
        return $this->publishDate;
    }

    public function setPublishDate(?int $publishDate): void
    {
        $this->publishDate = $publishDate;
    }

    /**
     * Returns the publish date if set, otherwise the creation date.
     */
    public function getEffectiveDate(): int
    {
        if (($this->publishDate ?? 0) > 0) {
            return $this->publishDate;
        }

        return $this->creationDate ?? 0;
    }

    /**
     * @return ObjectStorage<ContentElement>
     */
    public function getContentElements(): ObjectStorage
    {
        return $this->contentElements;
    }

    /**
     * @param ObjectStorage<ContentElement> $contentElements
     */
    public function setContentElements(ObjectStorage $contentElements): void
    {
        $this->contentElements = $contentElements;
    }

    public function addContentElement(ContentElement $contentElement): void
    {
        $this->contentElements->attach($contentElement);
    }

    public function removeContentElement(ContentElement $contentElement): void
    {
        $this->contentElements->detach($contentElement);
    }

    /**
     * @return ObjectStorage<Tag>
     */
    public function getTags(): ObjectStorage
    {
        return $this->tags;
    }

    /**
     * @param ObjectStorage<Tag> $tags
     */
    public function setTags(ObjectStorage $tags): void
    {
        $this->tags = $tags;
    }

    public function addTag(Tag $tag): void
    {
        $this->tags->attach($tag);
    }

    public function removeTag(Tag $tag): void
    {
        $this->tags->detach($tag);
    }

    public function isArchived(): bool
    {
        return $this->archived;
    }

    public function setArchived(bool $archived): void
    {
        $this->archived = $archived;
    }

    public function isTopPost(): bool
    {
        return $this->topPost;
    }

    public function setTopPost(bool $topPost): void
    {
        $this->topPost = $topPost;
    }
}
