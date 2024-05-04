<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Factory\BookFactory;
use App\Factory\AuthorFactory;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        AuthorFactory::createMany(10, ['books' => BookFactory::new()->many(1, 4)]);
    }
}
