<?php

namespace App\Tests\Entity;

use App\Entity\Formation;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class FormationValidationTest extends KernelTestCase
{
    public function testPublishedAtInFutureIsInvalid()
    {
        self::bootKernel();
        $container = static::getContainer();
        $validator = $container->get('validator');

        $formation = new Formation();
        $formation->setTitle('Titre de test');
        $formation->setPublishedAt(new \DateTime('+2 days')); 

        $errors = $validator->validate($formation);

        $this->assertGreaterThan(0, count($errors), 'Une date future devrait générer une erreur de validation');
    }

    public function testPublishedAtTodayIsValid()
    {
        self::bootKernel();
        $container = static::getContainer();
        $validator = $container->get('validator');

        $formation = new Formation();
        $formation->setTitle('Titre de test');
        $formation->setPublishedAt(new \DateTime('today'));

        $errors = $validator->validate($formation);

        $this->assertCount(0, $errors, 'Une date du jour ne doit pas générer d’erreur');
    }
}
