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
        'Policier'
    ];

    public function load(ObjectManager $manager): void
    {
        foreach (self::GENRES as $index => $genreName) {
            $genre = new Genres();
            $genre->setNom($genreName);
            $manager->persist($genre);

            // Enregistre une reference reutilisee ensuite par LivreFixtures pour relier chaque livre a son genre.
            $this->addReference('genre_' . $index, $genre);
        }

        $manager->flush();
    }
}
