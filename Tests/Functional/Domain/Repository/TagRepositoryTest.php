<?php

declare(strict_types=1);

/*
 * This file is part of the TYPO3 extension "golb".
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 3
 * of the License, or any later version.
 */

namespace Greenfieldr\Golb\Tests\Functional\Domain\Repository;

use Greenfieldr\Golb\Domain\Model\Tag;
use Greenfieldr\Golb\Domain\Repository\TagRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

#[CoversClass(TagRepository::class)]
#[CoversClass(Tag::class)]
final class TagRepositoryTest extends FunctionalTestCase
{
    protected array $testExtensionsToLoad = ['greenfieldr/golb'];

    private TagRepository $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->importCSVDataSet(__DIR__ . '/../../Fixtures/BlogSite.csv');
        $this->subject = $this->get(TagRepository::class);
    }

    /**
     * @param Tag[] $tags
     * @return string[]
     */
    private function titles(array $tags): array
    {
        return array_map(static fn(Tag $tag): string => $tag->getTitle(), $tags);
    }

    #[Test]
    public function findAllIgnoresStoragePageAndSortsByTitle(): void
    {
        /** @var Tag[] $tags */
        $tags = $this->subject->findAll()->toArray();

        self::assertSame(['php', 'typo3', 'unused'], $this->titles($tags));
    }

    #[Test]
    public function findAllWithAtLeastOneBlogPostSkipsUnusedTags(): void
    {
        $tags = $this->subject->findAllWithAtLeastOneBlogPost();

        self::assertSame(['php', 'typo3'], $this->titles($tags));
        self::assertCount(1, $tags[0]->getPages());
        self::assertCount(2, $tags[1]->getPages());
        self::assertSame('php', $tags[0]->getSlug());
        self::assertSame('', $tags[0]->getDescription());
    }
}
