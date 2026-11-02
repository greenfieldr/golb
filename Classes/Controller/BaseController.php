<?php

declare(strict_types=1);

namespace Greenfieldr\Golb\Controller;

/*
 * This file is part of TYPO3 CMS-based extension "golb".
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 3
 * of the License, or any later version.
 */

use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

/**
 * Class BaseController
 *
 * @package Greenfieldr\Golb\Domain\Controller
 */
class BaseController extends ActionController
{

    /**
     * @var ContentObjectRenderer
     */
    protected $contentObject;

    /**
     * Initialize action sets content object
     *
     * @return void
     */
    public function initializeAction(): void
    {
        $this->contentObject = $this->request->getAttribute('currentContentObject');
    }
}
