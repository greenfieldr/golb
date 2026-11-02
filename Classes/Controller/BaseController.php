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

abstract class BaseController extends ActionController
{
    protected ?ContentObjectRenderer $contentObject = null;

    public function initializeAction(): void
    {
        $contentObject = $this->request->getAttribute('currentContentObject');
        $this->contentObject = $contentObject instanceof ContentObjectRenderer ? $contentObject : null;
    }

    /**
     * Returns the record (tt_content row) of the currently rendered plugin.
     *
     * @return array<string, mixed>
     */
    protected function getContentObjectData(): array
    {
        return $this->contentObject->data ?? [];
    }
}
