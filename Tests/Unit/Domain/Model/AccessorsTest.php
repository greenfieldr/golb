<?php

declare(strict_types=1);

/*
 * This file is part of the TYPO3 extension "golb".
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 3
 * of the License, or any later version.
 */

namespace Greenfieldr\Golb\Tests\Unit\Domain\Model;

use Greenfieldr\Golb\Domain\Model\ContentElement;
use Greenfieldr\Golb\Domain\Model\Page;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * Round-trip test for the plain accessors of the models mapped onto core
 * tables, which guards against mismatched getter and setter pairs.
 */
#[CoversClass(Page::class)]
#[CoversClass(ContentElement::class)]
final class AccessorsTest extends UnitTestCase
{
    /**
     * @return iterable<string, array{class-string<AbstractEntity>, string, mixed}>
     */
    public static function accessorDataProvider(): iterable
    {
        $page = [
            'title' => 'Title', 'subtitle' => 'Subtitle', 'navTitle' => 'Nav', 'navigationTitle' => 'Nav 2',
            'abstract' => 'Abstract', 'description' => 'Description', 'keywords' => 'a, b',
            'author' => 'Author', 'authorName' => 'Author 2', 'authorEmail' => 'mail@example.org',
            'backendLayout' => 'pagets__default', 'backendLayoutNextLevel' => 'pagets__next',
            'cacheTags' => 'tag', 'cacheTimeout' => 3600, 'contentFromPid' => 5, 'creationDate' => 100,
            'doktype' => 41, 'documentType' => 1, 'startTime' => 10, 'endTime' => 20,
            'extendToSubpages' => true, 'feGroup' => '1,2', 'navHide' => true, 'lastUpdated' => 30,
            'layout' => 2, 'mountPid' => 7, 'mountPidOverlay' => 8, 'newUntil' => 40, 'noSearch' => true,
            'shortcut' => 9, 'shortcutMode' => 1, 'siteRoot' => true, 'sorting' => 256, 'target' => '_blank',
            'tstamp' => 50, 'modificationDate' => 60, 'url' => 'https://example.org', 'publishDate' => 70,
            'archived' => true, 'topPost' => true,
        ];
        foreach ($page as $property => $value) {
            yield 'Page::' . $property => [Page::class, $property, $value];
        }

        $contentElement = [
            'bodyText' => 'Text', 'cType' => 'text', 'crDate' => 1, 'crUserId' => 2, 'date' => 3,
            'deleted' => true, 'endTime' => 4, 'feGroup' => '1', 'header' => 'Header', 'headerLink' => 't3://page?uid=1',
            'hidden' => true, 'image' => '1', 'layout' => 2, 'listType' => 'plugin', 'media' => '3', 'sorting' => 4,
            'subHeader' => 'Sub', 'startTime' => 5, 'target' => '_top', 'timestamp' => 6,
        ];
        foreach ($contentElement as $property => $value) {
            yield 'ContentElement::' . $property => [ContentElement::class, $property, $value];
        }
    }

    /**
     * @param class-string<AbstractEntity> $className
     */
    #[Test]
    #[DataProvider('accessorDataProvider')]
    public function valueSetBySetterIsReturnedByGetter(string $className, string $property, mixed $value): void
    {
        $subject = new $className();
        $setter = 'set' . ucfirst($property);
        $getter = is_bool($value) ? 'is' . ucfirst($property) : 'get' . ucfirst($property);
        if (!method_exists($subject, $getter)) {
            $getter = 'get' . ucfirst($property);
        }

        self::assertTrue(method_exists($subject, $setter), 'Missing setter ' . $setter);
        self::assertTrue(method_exists($subject, $getter), 'Missing getter ' . $getter);

        (new \ReflectionMethod($subject, $setter))->invoke($subject, $value);

        self::assertSame($value, (new \ReflectionMethod($subject, $getter))->invoke($subject));
    }
}
