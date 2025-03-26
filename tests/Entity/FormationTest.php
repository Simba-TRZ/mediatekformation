<?php

namespace App\Tests\Entity;

use App\Entity\Formation;
use PHPUnit\Framework\TestCase;

class FormationTest extends TestCase
{
    public function testGetPublishedAtString()
    {
        $formation = new Formation();
        $date = new \DateTime('2025-03-24');
        $formation->setPublishedAt($date);
        $result = $formation->getPublishedAtString();
        $this->assertEquals('24/03/2025', $result);
    }

    public function testGetPublishedAtStringWithNullDate()
    {
        $formation = new Formation();
        $result = $formation->getPublishedAtString();
        $this->assertEquals('', $result);
    }
}
