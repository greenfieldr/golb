<?php

/*
 * This file is part of the TYPO3 extension "golb".
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 3
 * of the License, or any later version.
 */

namespace Greenfieldr\Golb\Domain\Model;

use TYPO3\CMS\Extbase\Annotation\ORM\Lazy;
use TYPO3\CMS\Extbase\Domain\Model\FileReference;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\Generic\LazyLoadingProxy;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/**
 * Maps on table 'pages'
 */
class Page extends AbstractEntity
{
    /**
     * Contains page abstract
     *
     * @var string $abstract
     */
    protected string $abstract = '';

    /**
     * Contains URL alias
     *
     * @var string $alias
     */
    protected string $alias = '';

    /**
     * Contains author email
     * Use golb:author.email view helper to switch between this field and
     * corresponding backend user based in plugin configuration
     *
     * @var string $authorEmail
     */
    protected string $authorEmail = '';

    /**
     * Contains author image
     *
     * @var FileReference|LazyLoadingProxy
     */
    #[Lazy]
    protected FileReference|LazyLoadingProxy $authorImage;

    /**
     * Contains author name
     * Use golb:author.name view helper to switch between this field and
     * corresponding backend user based in plugin configuration
     *
     * @var string $author
     */
    protected string $author = '';

    /**
     * Contains selected backend layout
     *
     * @var string $backendLayout
     */
    protected string $backendLayout = '';

    /**
     * Contains backend layout for sub level
     *
     * @var string $backendLayoutNextLevel
     */
    protected string $backendLayoutNextLevel = '';

    /**
     * Tags the cached page belongs to
     *
     * @var string $cacheTags
     */
    protected string $cacheTags = '';

    /**
     * Timestamp for cache timeout
     *
     * @var ?int $cacheTimeout
     */
    protected ?int $cacheTimeout = null;

    /**
     * Contains relations to selected categories for this page
     *
     * @var ObjectStorage<Category>
     */
    #[Lazy]
    protected ObjectStorage $categories;

    /**
     * Contains page id to show content from instead of the current page content itself
     *
     * @var ?int $contentFromPid
     */
    protected ?int $contentFromPid = null;

    /**
     * @var ?int
     */
    protected ?int $creationDate = null;

    /**
     * Create user identifier
     *
     * @var ?int $cruserId
     */
    protected ?int $cruserId = null;

    /**
     * Deleted flag
     *
     * @var bool $deleted
     */
    protected bool $deleted;

    /**
     * Meta description for this page
     *
     * @var string $description
     */
    protected string $description = '';

    /**
     * Page type
     *
     * @var ?int $doktype
     */
    protected ?int $doktype = null;

    /**
     * End time timestamp
     * Maps on field "endtime"
     *
     * @var ?int $endTime
     */
    protected ?int $endtime = null;

    /**
     * Flag to extend publish dates and access rights to subpages
     * Maps on field "extendToSubpages"
     *
     * @var bool $extendToSubPages
     */
    protected bool $extendToSubPages;

    /**
     * Frontend user groups
     *
     * @var string $feGroup
     */
    protected string $feGroup = '';

    /**
     * Hidden flag
     *
     * @var bool $hidden
     */
    protected bool $hidden;

    /**
     * Flag to hide page in navigation
     * Maps on field "nav_hide"
     *
     * @var bool $navHide
     */
    protected bool $navHide;

    /**
     * Keywords for this page
     *
     * @var string $keywords
     */
    protected string $keywords = '';

    /**
     * Last updated timestamp
     * Maps on field "lastUpdated" (Note: CamelCase instead of underscores)
     *
     * @var ?int $lastUpdated
     */
    protected ?int $lastUpdated = null;

    /**
     * Selected page layout
     *
     * @var ?int $layout
     */
    protected ?int $layout = null;

    /**
     * @var ObjectStorage<FileReference>
     */
    #[Lazy]
    protected ObjectStorage $media;

    /**
     * Contains page identifier from mount point if document type is set to mount point
     *
     * @var ?int $mountPid
     */
    protected ?int $mountPid = null;

    /**
     * Contains mount point overlay if needed
     *
     * @var ?int $mountPidOl
     */
    protected ?int $mountPidOl = null;

    /**
     * Contains navigation title
     *
     * @var string $navTitle
     */
    protected string $navTitle = '';

    /**
     * Timestamp how long the page is flagged as new
     * Maps on field "newUntil" (Note: CamelCase instead of underscores)
     *
     * @var ?int $newUntil
     */
    protected ?int $newUntil = null;

    /**
     * Flag indicates if the page should be cached
     *
     * @var bool $noCache
     */
    protected bool $noCache;

    /**
     * Flag to hide page from search results
     *
     * @var bool $noSearch
     */
    protected bool $noSearch;

    /**
     * Contains related pages
     *
     * @var ObjectStorage<Page>
     */
    #[Lazy]
    protected ObjectStorage $relatedPages;

    /**
     * Shortcut target for document type shortcut depending on shortcut mode
     *
     * @var ?int $shortcut
     */
    protected ?int $shortcut = null;

    /**
     * Shortcut mode if document type shortcut is selected
     *
     * @var ?int $shortcutMode
     */
    protected ?int $shortcutMode = null;

    /**
     * Is site root flag
     *
     * @var bool $siteRoot
     */
    protected bool $isSiteroot;

    /**
     * Sorting field
     *
     * @var ?int $sorting
     */
    protected ?int $sorting = null;

    /**
     * Start time timestamp
     *
     * @var ?int $startTime
     */
    protected ?int $starttime = null;

    /**
     * List of subpages
     *
     * @var ObjectStorage<Page>
     */
    #[Lazy]
    protected ObjectStorage $subpages;

    /**
     * Page subtitle
     *
     * @var string $subtitle
     */
    protected string $subtitle = '';

    /**
     * Contains anchor link target for links to this page
     *
     * @var string $target
     */
    protected string $target = '';

    /**
     * Contains timestamp
     *
     * @var ?int $tstamp
     */
    protected ?int $tstamp = null;

    /**
     * Page title
     *
     * @var string $title
     */
    protected string $title = '';

    /**
     * URL e.g. for document type external
     *
     * @var string $url
     */
    protected string $url = '';

    /**
     * Selected URL scheme
     *
     * @var ?int $urlScheme
     */
    protected ?int $urlScheme = null;

    /**
     * URL type
     *
     * @var ?int $urlType
     */
    protected ?int $urltype = null;

    /**
     * @var ?int $publishDate
     */
    protected ?int $publishDate = null;

    /**
     * List of content elements
     *
     * @var ObjectStorage<ContentElement>
     */
    #[Lazy]
    protected ObjectStorage $contentElements;

    /**
     * Contains tags for this page
     *
     * @var ObjectStorage<Tag>
     */
    #[Lazy]
    protected ObjectStorage $tags;

    /**
     * @var bool
     */
    protected bool $archived = false;

    /**
     * @var bool
     */
    protected bool $topPost = false;

    /**
     * The constructor
     */
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

    /**
     * Returns author name
     *
     * @return ?string
     */
    public function getAuthorName(): ?string
    {
        return $this->author;
    }

    /**
     * Sets author name
     *
     * @param string $authorName
     */
    public function setAuthorName(string $authorName): void
    {
        $this->author = $authorName;
    }

    /**
     * Adds a category
     *
     * @param Category $category
     */
    public function addCategory(Category $category): void
    {
        $this->categories->attach($category);
    }

    /**
     * Removes a category
     *
     * @param Category $category
     */
    public function removeCategory(Category $category): void
    {
        $this->categories->detach($category);
    }

    /**
     * @return ?ObjectStorage
     */
    public function getCategories(): ?ObjectStorage
    {
        return $this->categories;
    }

    /**
     * @param ObjectStorage $categories
     */
    public function setCategories(ObjectStorage $categories): void
    {
        $this->categories = $categories;
    }

    /**
     * Returns create date timestamp
     *
     * @return ?int
     */
    public function getCreationDate(): ?int
    {
        return $this->creationDate;
    }

    /**
     * Sets create date
     *
     * @param int $creationDate
     */
    public function setCreationDate(int $creationDate): void
    {
        $this->creationDate = $creationDate;
    }

    /**
     * Returns create user identifier
     *
     * @return ?int
     */
    public function getCreateUserId(): ?int
    {
        return $this->cruserId;
    }

    /**
     * Sets create user identifier
     *
     * @param int $createUserId
     */
    public function setCreateUserId(int $createUserId): void
    {
        $this->cruserId = $createUserId;
    }

    /**
     * Returns deleted flag
     *
     * @return bool
     */
    public function isDeleted(): bool
    {
        return $this->deleted;
    }

    /**
     * Returns document type
     *
     * @return ?int
     */
    public function getDocumentType(): ?int
    {
        return $this->doktype;
    }

    /**
     * Sets document type
     *
     * @param int $documentType
     */
    public function setDocumentType(int $documentType): void
    {
        $this->doktype = $documentType;
    }

    /**
     * Returns end time
     *
     * @return ?int
     */
    public function getEndTime(): ?int
    {
        return $this->endtime;
    }

    /**
     * Sets end time
     *
     * @param int $endTime
     */
    public function setEndTime(int $endTime): void
    {
        $this->endtime = $endTime;
    }

    /**
     * Returns true if page is hidden
     *
     * @return bool
     */
    public function isHidden(): bool
    {
        return $this->hidden;
    }

    /**
     * Returns true if page should be hidden in navigation
     *
     * @return bool
     */
    public function isNavHide(): bool
    {
        return $this->navHide;
    }

    /**
     * Sets flag to hide page in navigation
     *
     * @param bool $navHide
     */
    public function setNavHide(bool $navHide): void
    {
        $this->navHide = $navHide;
    }

    /**
     * Returns overlay for mount point page identifier
     *
     * @return ?int
     */
    public function getMountPidOverlay(): ?int
    {
        return $this->mountPidOl;
    }

    /**
     * Sets page identifier for mount point overlay
     *
     * @param int $mountPidOverlay
     */
    public function setMountPidOverlay(int $mountPidOverlay): void
    {
        $this->mountPidOl = $mountPidOverlay;
    }

    /**
     * Returns navigation title
     * Returns page title if navigation title is empty
     *
     * @return string
     */
    public function getNavigationTitle(): string
    {
        return $this->navTitle;
    }

    /**
     * Sets navigation title
     *
     * @param string $navigationTitle
     */
    public function setNavigationTitle(string $navigationTitle): void
    {
        $this->navTitle = $navigationTitle;
    }

    /**
     * Adds a related page
     *
     * @param Page $relatedPage
     */
    public function addRelatedPage(Page $relatedPage): void
    {
        $this->relatedPages->attach($relatedPage);
    }

    /**
     * Removes a related page
     *
     * @param Page $relatedPage
     */
    public function removeRelatedPage(Page $relatedPage): void
    {
        $this->relatedPages->detach($relatedPage);
    }

    /**
     * Returns true if page is the site root
     *
     * @return bool
     */
    public function isSiteRoot(): bool
    {
        return $this->isSiteroot;
    }

    /**
     * Sets flag for site root
     *
     * @param bool $siteRoot
     */
    public function setSiteRoot(bool $siteRoot): void
    {
        $this->isSiteroot = $siteRoot;
    }

    /**
     * Returns start time timestamp for publishing
     *
     * @return ?int
     */
    public function getStartTime(): ?int
    {
        return $this->starttime;
    }

    /**
     * Sets timestamp for start time
     *
     * @param int $startTime
     */
    public function setStartTime(int $startTime): void
    {
        $this->starttime = $startTime;
    }

    /**
     * Adds a subpage
     *
     * @param Page $subpage
     */
    public function addSubpage(Page $subpage): void
    {
        $this->subpages->attach($subpage);
    }

    /**
     * Removes a subpage
     *
     * @param Page $subpage
     */
    public function removeSubpage(Page $subpage): void
    {
        $this->subpages->detach($subpage);
    }

    /**
     * @return ?ObjectStorage
     */
    public function getSubpages(): ?ObjectStorage
    {
        return $this->subpages;
    }

    /**
     * @param ObjectStorage $subpages
     */
    public function setSubpages(ObjectStorage $subpages): void
    {
        $this->subpages = $subpages;
    }

    /**
     * Returns timestamp of last system update of the page
     *
     * @return ?int
     */
    public function getModificationDate(): ?int
    {
        return $this->tstamp;
    }

    /**
     * Sets timestamp of last system update
     *
     * @param int $timestamp
     */
    public function setModificationDate(int $timestamp): void
    {
        $this->tstamp = $timestamp;
    }

    /**
     * Returns URL type
     *
     * @return ?int
     */
    public function getUrlType(): ?int
    {
        return $this->urltype;
    }

    /**
     * Sets URL type
     *
     * @param int $urlType
     */
    public function setUrlType(int $urlType): void
    {
        $this->urltype = $urlType;
    }

    /**
     * Adds a content element
     *
     * @param ContentElement $contentElement
     */
    public function addContentElement(ContentElement $contentElement): void
    {
        $this->contentElements->attach($contentElement);
    }

    /**
     * Removes a content element
     *
     * @param ContentElement $contentElement
     */
    public function removeContentElement(ContentElement $contentElement): void
    {
        $this->contentElements->detach($contentElement);
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
    public function getAbstract(): string
    {
        return $this->abstract;
    }

    /**
     * @param string $abstract
     */
    public function setAbstract(string $abstract): void
    {
        $this->abstract = $abstract;
    }

    /**
     * @return string
     */
    public function getAlias(): string
    {
        return $this->alias;
    }

    /**
     * @param string $alias
     */
    public function setAlias(string $alias): void
    {
        $this->alias = $alias;
    }

    /**
     * @return string
     */
    public function getAuthorEmail(): string
    {
        return $this->authorEmail;
    }

    /**
     * @param string $authorEmail
     */
    public function setAuthorEmail(string $authorEmail): void
    {
        $this->authorEmail = $authorEmail;
    }

    /**
     * @return ?FileReference
     */
    public function getAuthorImage(): ?FileReference
    {
        if ($this->authorImage instanceof LazyLoadingProxy) {
            $this->authorImage->_loadRealInstance();
        }
        return $this->authorImage;
    }

    /**
     * @param FileReference $authorImage
     */
    public function setAuthorImage(FileReference $authorImage): void
    {
        $this->authorImage = $authorImage;
    }

    /**
     * @return string
     */
    public function getAuthor(): string
    {
        return $this->author;
    }

    /**
     * @param string $author
     */
    public function setAuthor(string $author): void
    {
        $this->author = $author;
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
    public function getKeywords(): string
    {
        return $this->keywords;
    }

    /**
     * @param string $keywords
     */
    public function setKeywords(string $keywords): void
    {
        $this->keywords = $keywords;
    }

    /**
     * @return ?int
     */
    public function getLastUpdated(): ?int
    {
        return $this->lastUpdated;
    }

    /**
     * @param int $lastUpdated
     */
    public function setLastUpdated(int $lastUpdated): void
    {
        $this->lastUpdated = $lastUpdated;
    }

    /**
     * @return ?int
     */
    public function getLayout(): ?int
    {
        return $this->layout;
    }

    /**
     * @param int $layout
     */
    public function setLayout(int $layout): void
    {
        $this->layout = $layout;
    }

    /**
     * @return ?ObjectStorage
     */
    public function getMedia(): ?ObjectStorage
    {
        return $this->media;
    }

    /**
     * @param ObjectStorage $media
     */
    public function setMedia(ObjectStorage $media): void
    {
        $this->media = $media;
    }

    /**
     * @return string
     */
    public function getNavTitle(): string
    {
        return $this->navTitle;
    }

    /**
     * @param string $navTitle
     */
    public function setNavTitle(string $navTitle): void
    {
        $this->navTitle = $navTitle;
    }

    /**
     * @return ?int
     */
    public function getNewUntil(): ?int
    {
        return $this->newUntil;
    }

    /**
     * @param int $newUntil
     */
    public function setNewUntil(int $newUntil): void
    {
        $this->newUntil = $newUntil;
    }

    /**
     * @return ?ObjectStorage
     */
    public function getRelatedPages(): ?ObjectStorage
    {
        return $this->relatedPages;
    }

    /**
     * @param ObjectStorage $relatedPages
     */
    public function setRelatedPages(ObjectStorage $relatedPages): void
    {
        $this->relatedPages = $relatedPages;
    }

    /**
     * @return ?int
     */
    public function getSorting(): ?int
    {
        return $this->sorting;
    }

    /**
     * @param int $sorting
     */
    public function setSorting(int $sorting): void
    {
        $this->sorting = $sorting;
    }

    /**
     * @return string
     */
    public function getSubtitle(): string
    {
        return $this->subtitle;
    }

    /**
     * @param string $subtitle
     */
    public function setSubtitle(string $subtitle): void
    {
        $this->subtitle = $subtitle;
    }

    /**
     * @return string
     */
    public function getTarget(): string
    {
        return $this->target;
    }

    /**
     * @param string $target
     */
    public function setTarget(string $target): void
    {
        $this->target = $target;
    }

    /**
     * @return ?int
     */
    public function getTstamp(): ?int
    {
        return $this->tstamp;
    }

    /**
     * @param int $tstamp
     */
    public function setTstamp(int $tstamp): void
    {
        $this->tstamp = $tstamp;
    }

    /**
     * @param int $uid
     */
    public function setUid(int $uid): void
    {
        $this->uid = $uid;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @param string $url
     */
    public function setUrl(string $url): void
    {
        $this->url = $url;
    }

    /**
     * @return ?int
     */
    public function getDoktype(): ?int
    {
        return $this->doktype;
    }

    /**
     * @param int $doktype
     */
    public function setDoktype(int $doktype): void
    {
        $this->doktype = $doktype;
    }

    /**
     * @return ?int
     */
    public function getUrlScheme(): ?int
    {
        return $this->urlScheme;
    }

    /**
     * @param int $urlScheme
     */
    public function setUrlScheme(int $urlScheme): void
    {
        $this->urlScheme = $urlScheme;
    }

    /**
     * @return ?ObjectStorage
     */
    public function getContentElements(): ?ObjectStorage
    {
        return $this->contentElements;
    }

    /**
     * @param ObjectStorage $contentElements
     */
    public function setContentElements(ObjectStorage $contentElements): void
    {
        $this->contentElements = $contentElements;
    }

    /**
     * @return ?int
     */
    public function getPublishDate(): ?int
    {
        return $this->publishDate;
    }

    /**
     * @param int $publishDate
     */
    public function setPublishDate(int $publishDate): void
    {
        $this->publishDate = $publishDate;
    }

    /**
     * Adds a tag
     *
     * @param Tag $tag
     */
    public function addTag(Tag $tag): void
    {
        $this->tags->attach($tag);
    }

    /**
     * Removes a tag
     *
     * @param Tag $tag
     */
    public function removeTag(Tag $tag): void
    {
        $this->tags->detach($tag);
    }

    /**
     * @return ?ObjectStorage
     */
    public function getTags(): ?ObjectStorage
    {
        return $this->tags;
    }

    /**
     * @param ObjectStorage $tags
     */
    public function setTags(ObjectStorage $tags): void
    {
        $this->tags = $tags;
    }

    /**
     * @return bool
     */
    public function isArchived(): bool
    {
        return $this->archived === true;
    }

    /**
     * @param bool $archived
     */
    public function setArchived(bool $archived): void
    {
        $this->archived = $archived;
    }

    /**
     * @return bool
     */
    public function isTopPost(): bool
    {
        return $this->topPost;
    }

    /**
     * @param bool $topPost
     */
    public function setTopPost(bool $topPost): void
    {
        $this->topPost = $topPost;
    }

}
