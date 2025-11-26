<?php

namespace App\DataFixtures;

use App\Entity\Livres;
use App\Entity\Genres;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class LivreFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $jsonPath = __DIR__ . '/data/livres.json';

        if (!file_exists($jsonPath)) {
            throw new \Exception("Le fichier livres.json est introuvable : $jsonPath");
        }

        $data = json_decode(file_get_contents($jsonPath), true);

        if (!is_array($data)) {
            throw new \Exception("Le contenu de livres.json est invalide.");
        }

        foreach ($data as $item) {

            // Trouver l’index correspondant au nom du genre
            $genreIndex = array_search($item['genre'], GenreFixtures::GENRES);

            if ($genreIndex === false) {
                throw new \Exception("Genre introuvable : '" . $item['genre'] . "'. Vérifie GenreFixtures::GENRES.");
            }

            $livre = new Livres();
            $livre->setTitre($item['titre']);
            $livre->setAuteur($item['auteur']);
            $livre->setEditeur($item['editeur']);
            $livre->setResume($item['resume']);
            $livre->setDatePublication(new \DateTimeImmutable($item['datePublication']));
            $livre->setNbPages($item['nbPages']);
            $livre->setPrix($item['prix']);
            $livre->setPhoto($item['photo']);
            $livre->setDisponibilite($item['disponibilite']);
            $livre->setAgeAutorise($item['ageAutorise']);
            $livre->setIsBestSeller($item['isBestSeller']);

            // DoctrineFixturesBundle (version 3.x) exige 2 arguments ici
            /** @var Genres $genre */
            $genre = $this->getReference('genre_' . $genreIndex, Genres::class);

            // Associer le genre
            $livre->setGenre($genre);

            $manager->persist($livre);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            GenreFixtures::class
        ];
    }
}
