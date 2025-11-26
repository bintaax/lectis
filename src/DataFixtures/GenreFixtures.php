<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class GenreFixtures extends Fixture
{

    public const GENRES  = [
        'Romance',
        'Poésie',
        'Jeunesse',
        'BD/Mangas',
        'Fantasy',
        'Horreur',
        'Thriller'
    ];

    public function load(ObjectManager $manager): void
    {
   
        foreach (self::GENRES as $genreName) {
            $genre = new \App\Entity\Genres();
            $genre->setNom($genreName);
            $manager->persist($genre);
        }

        $manager->flush();
    }
}
