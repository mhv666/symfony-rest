<?php

namespace App\DataFixtures;

use DateTimeImmutable;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

use Doctrine\ORM\EntityManagerInterface;

class AppFixtures extends Fixture
{
    public function __construct(EntityManagerInterface $em)
    {
    }
    public function load(ObjectManager $manager)
    {
    }
}
