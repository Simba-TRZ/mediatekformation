<?php

namespace App\Tests\Entity;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use App\Entity\Playlist;
use App\Entity\Formation;
use Doctrine\ORM\EntityManagerInterface;

class PlaylistIntegrationTest extends KernelTestCase
{
    private ?EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->entityManager = self::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    public function testPlaylistFormationRelation(): void
    {
        $playlist = new Playlist();
        $playlist->setName('Playlist Test');

        $formation = new Formation();
        $formation->setTitle('Formation Test');
        $formation->setPublishedAt(new \DateTime());
        $formation->setPlaylist($playlist);

        $playlist->addFormation($formation);

        $this->entityManager->persist($playlist);
        $this->entityManager->persist($formation);
        $this->entityManager->flush();

        // Vérifie que la formation est bien liée à la playlist
        $this->assertEquals(1, $playlist->getFormations()->count());
        $this->assertSame($playlist, $formation->getPlaylist());
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->entityManager->close();
        $this->entityManager = null;
    }
}
