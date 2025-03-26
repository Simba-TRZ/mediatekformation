<?php

namespace App\Tests\Repository;

use App\Entity\Playlist;
use App\Repository\PlaylistRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class PlaylistRepositoryTest extends KernelTestCase
{
    private ?PlaylistRepository $repository;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->repository = static::getContainer()->get(PlaylistRepository::class);
    }

    public function testFindAllOrderByName(): void
    {
        $playlists = $this->repository->findAllOrderByName('ASC');
        $this->assertIsArray($playlists);
        $this->assertNotEmpty($playlists);
        $this->assertInstanceOf(Playlist::class, $playlists[0]);
    }

    public function testFindAllSortedBy(): void
    {
        $playlists = $this->repository->findAllSortedBy('description', 'DESC');
        $this->assertIsArray($playlists);
        $this->assertNotEmpty($playlists);
    }

    public function testFindByContainValueWithValidField(): void
    {
        $results = $this->repository->findByContainValue('name', 'html');
        $this->assertIsArray($results);
    }

    public function testFindByContainValueWithInvalidField(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->repository->findByContainValue('invalidField', 'html');
    }

    public function testFindByContainValueWithCategory(): void
    {
        $results = $this->repository->findByContainValue('name', 'html', 'categories');
        $this->assertIsArray($results);
    }
}
