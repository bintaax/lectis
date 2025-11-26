<?php

namespace App\DataFixtures;

use App\Entity\Genres;
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
        foreach (self::GENRES as $index => $genreName) {
            $genre = new Genres();
            $genre->setNom($genreName);
            $manager->persist($genre);

            // 📌 SUPER IMPORTANT : créer la référence pour les livres
            $this->addReference('genre_' . $index, $genre);
        }

        $manager->flush();
    }
}
