<?php

namespace App\DataFixtures;

use App\Factory\PlayerFactory;
use App\Factory\TournamentFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);
        $players = PlayerFactory::createMany(20);
        TournamentFactory::createMany(30);
        TournamentFactory::createMany(30,[
            'isFinished' => true
        ]);
        TournamentFactory::createOne([
            'players' => PlayerFactory::createMany(10)
        ]);
        TournamentFactory::createOne([
            'players' => PlayerFactory::createMany(4)
        ]);


        $manager->flush();
    }
}
