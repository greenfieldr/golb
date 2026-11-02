<?php

declare(strict_types=1);

namespace Greenfieldr\Golb\Domain\Model;

/*
 * This file is part of TYPO3 CMS-based extension "golb".
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 3
 * of the License, or any later version.
 */

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

/**
 * Maps on table 'pages'
 *
 * @author     Sascha Zander <sascha.zander@denkwerk.com>
 * @copyright  2015 Copyright belongs to the respective authors
 * @license    http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 * @package Greenfieldr\Golb\Domain\Model
 */
class ContentElement extends AbstractEntity
{

    /**
     * Contains body text
     * Maps on field "bodytext"
     *
     * @var string $bodyText
     */
    protected string $bodyText = '';

    /**
     * Create date timestamp
     * Maps on field "crdate"
     *
     * @var int $crdate
     */
    protected int $crDate = 0;

    /**
     * Create user identifier
     * Maps on field "cruserId"
     *
     * @var int $crUserId
     */
    protected int $crUserId = 0;

    /**
     * Content Type
     * Maps on field "CType"
     *
     * @var string $cType
     */
    protected string $cType = '';

    /**
     * Contains body text
     *
     * @var int $date
     */
    protected int $date = 0;

    /**
     * Deleted flag
     *
     * @var bool $deleted
     */
    protected bool $deleted = false;

    /**
     * End time timestamp
     * Maps on field "endtime"
     *
     * @var int $endTime
     */
    protected int $endTime = 0;

    /**
     * Frontend user groups
     * Maps on field "fe_group"
     *
     * @var string $feGroup
     */
    protected string $feGroup = '';

    /**
     * Contains header text
     *
     * @var string $header
     */
    protected string $header = '';

    /**
     * Contains header link
     * Maps on field "header_link"
     *
     * @var string $headerLink
     */
    protected string $headerLink = '';

    /**
     * Hidden flag
     *
     * @var bool $hidden
     */
    protected bool $hidden = false;

    /**
     * Contains referenced fal images for this page
     *
     * @var string $image
     */
    protected string $image = '';

    /**
     * Selected page layout
     *
     * @var int $layout
     */
    protected int $layout = 0;

    /**
     * Selected list type
     * Maps on field "list_type"
     *
     * @var string $listType
     */
    protected string $listType = '';

    /**
     * Contains referenced media assets for this page
     *
     * @var string $media
     */
    protected string $media = '';

    /**
     * Sorting field
     *
     * @var int $sorting
     */
    protected int $sorting = 0;

    /**
     * Contains body text
     * Maps on field "subheader"
     *
     * @var string $subHeader
     */
    protected string $subHeader = '';

    /**
     * Start time timestamp
     * Maps on field "starttime"
     *
     * @var int $startTime
     */
    protected int $startTime = 0;

    /**
     * Contains anchor link target for links to this page
     *
     * @var string $target
     */
    protected string $target = '';

    /**
     * Contains timestamp
     * Maps on field "tstamp"
     *
     * @var int $timestamp
     */
    protected int $timestamp = 0;

    /**
     * Returns body text
     *
     * @return string
     */
    public function getBodyText(): string
    {
        return $this->bodyText;
    }

    /**
     * Sets body text
     *
     * @param string $bodyText
     * @return void
     */
    public function setBodyText(string $bodyText): void
    {
        $this->bodyText = $bodyText;
    }

    /**
     * Returns content type
     *
     * @return string
     */
    public function getCType(): string
    {
        return $this->cType;
    }

    /**
     * Set content type
     *
     * @param string $cType
     * @return void
     */
    public function setCType(string $cType): void
    {
        $this->cType = $cType;
    }

    /**
     * Returns create date timestamp
     *
     * @return int
     */
    public function getCrDate(): int
    {
        return $this->crDate;
    }

    /**
     * Sets create date
     *
     * @param int $crDate
     * @return void
     */
    public function setCrDate(int $crDate): void
    {
        $this->crDate = $crDate;
    }

    /**
     * Returns create user identifier
     *
     * @return int
     */
    public function getCrUserId(): int
    {
        return $this->crUserId;
    }

    /**
     * Sets create user identifier
     *
     * @param int $crUserId
     * @return void
     */
    public function setCrUserId(int $crUserId): void
    {
        $this->crUserId = $crUserId;
    }

    /**
     * Returns date timestamp
     *
     * @return int
     */
    public function getDate(): int
    {
        return $this->date;
    }

    /**
     * Sets date
     *
     * @param int $date
     * @return void
     */
    public function setDate(int $date): void
    {
        $this->date = $date;
    }

    /**
     * Returns deleted flag
     *
     * @return bool
     */
    public function getDeleted(): bool
    {
        return $this->deleted;
    }

    /**
     * Sets deleted flag
     *
     * @param bool $deleted
     * @return void
     */
    public function setDeleted(bool $deleted): void
    {
        $this->deleted = $deleted;
    }

    /**
     * Returns end time
     *
     * @return int
     */
    public function getEndTime(): int
    {
        return $this->endTime;
    }

    /**
     * Sets end time
     *
     * @param int $endTime
     * @return void
     */
    public function setEndTime(int $endTime): void
    {
        $this->endTime = $endTime;
    }

    /**
     * Returns front end user groups
     *
     * @return string
     */
    public function getFeGroup(): string
    {
        return $this->feGroup;
    }

    /**
     * Sets frontend user groups
     *
     * @param string $feGroup
     * @return void
     */
    public function setFeGroup(string $feGroup): void
    {
        $this->feGroup = $feGroup;
    }

    /**
     * Returns header text
     *
     * @return string
     */
    public function getHeader(): string
    {
        return $this->header;
    }

    /**
     * Set header text
     *
     * @param string $header
     * @return void
     */
    public function setHeader(string $header): void
    {
        $this->header = $header;
    }

    /**
     * Returns header link
     *
     * @return string
     */
    public function getHeaderLink(): string
    {
        return $this->headerLink;
    }

    /**
     * Set header link
     *
     * @param string $headerLink
     * @return void
     */
    public function setHeaderLink(string $headerLink): void
    {
        $this->headerLink = $headerLink;
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
     * Sets hidden flag
     *
     * @param bool $hidden
     * @return void
     */
    public function setHidden(bool $hidden): void
    {
        $this->hidden = $hidden;
    }

    /**
     * Returns relations to fal images
     *
     * @return string
     */
    public function getImage(): string
    {
        return $this->image;
    }

    /**
     * Sets relations to fal images
     *
     * @param string $image
     * @return void
     */
    public function setImage(string $image): void
    {
        $this->image = $image;
    }

    /**
     * Returns selected frontend layout
     *
     * @return int
     */
    public function getLayout(): int
    {
        return $this->layout;
    }

    /**
     * Sets frontend layout
     *
     * @param int $layout
     * @return void
     */
    public function setLayout(int $layout): void
    {
        $this->layout = $layout;
    }

    /**
     * Returns list type
     *
     * @return string
     */
    public function getListType(): string
    {
        return $this->listType;
    }

    /**
     * Set list type
     *
     * @param string $listType
     * @return void
     */
    public function setListType(string $listType): void
    {
        $this->listType = $listType;
    }

    /**
     * Returns relations to media assets
     *
     * @return string
     */
    public function getMedia(): string
    {
        return $this->media;
    }

    /**
     * Sets relations to media assets
     *
     * @param string $media
     * @return void
     */
    public function setMedia(string $media): void
    {
        $this->media = $media;
    }

    /**
     * Returns sorting position
     *
     * @return int
     */
    public function getSorting(): int
    {
        return $this->sorting;
    }

    /**
     * Sets sorting position
     *
     * @param int $sorting
     * @return void
     */
    public function setSorting(int $sorting): void
    {
        $this->sorting = $sorting;
    }

    /**
     * Returns start time timestamp for publishing
     *
     * @return int
     */
    public function getStartTime(): int
    {
        return $this->startTime;
    }

    /**
     * Sets timestamp for start time
     *
     * @param int $startTime
     * @return void
     */
    public function setStartTime(int $startTime): void
    {
        $this->startTime = $startTime;
    }

    /**
     * Returns subheader text
     *
     * @return string
     */
    public function getSubHeader(): string
    {
        return $this->subHeader;
    }

    /**
     * Set subheader text
     *
     * @param string $subHeader
     * @return void
     */
    public function setSubHeader(string $subHeader): void
    {
        $this->subHeader = $subHeader;
    }

    /**
     * Returns anchor link target for anchors to this page
     *
     * @return string
     */
    public function getTarget(): string
    {
        return $this->target;
    }

    /**
     * Sets anchor link target for anchor to this page
     *
     * @param string $target
     * @return void
     */
    public function setTarget(string $target): void
    {
        $this->target = $target;
    }

    /**
     * Returns timestamp of last system update of the page
     *
     * @return int
     */
    public function getTimestamp(): int
    {
        return $this->timestamp;
    }

    /**
     * Sets timestamp of last system update
     *
     * @param int $timestamp
     * @return void
     */
    public function setTimestamp(int $timestamp): void
    {
        $this->timestamp = $timestamp;
    }
}
