<?php

declare(strict_types=1);

/*
 * This file is part of the TYPO3 extension "golb".
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 3
 * of the License, or any later version.
 */

namespace Greenfieldr\Golb\Controller;

use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

/**
 * Class BaseController
 */
class BaseController extends ActionController
{
    /**
     * @var ContentObjectRenderer
     */
    protected $contentObject;

    /**
     * Initialize action sets content object
     */
    public function initializeAction(): void
    {
        $this->contentObject = $this->request->getAttribute('currentContentObject');
    }
}
