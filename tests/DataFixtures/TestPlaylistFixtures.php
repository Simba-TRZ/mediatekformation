<?php

namespace App\Tests\DataFixtures;

use App\Entity\Playlist;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;

class TestPlaylistFixtures extends Fixture implements FixtureGroupInterface
{
    public function load(ObjectManager $manager): void
    {
        $playlist = new Playlist();
        $playlist->setName('Test Playlist');
        $playlist->setDescription('Description pour test');
        $manager->persist($playlist);
        
        $manager->flush();
    }
    
    public static function getGroups(): array
    {
        return ['test'];
    }
}