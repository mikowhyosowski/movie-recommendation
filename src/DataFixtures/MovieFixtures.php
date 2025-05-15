<?php

namespace App\DataFixtures;

use App\Entity\Movie;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class MovieFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $movies = include __DIR__ . '/../../data/movies.php';
        $movies = array_unique($movies); // Usuwa duplikaty (do potwierdzenia)

        foreach ($movies as $title) {
            $movie = new Movie();
            $movie->setTitle($title);
            $manager->persist($movie);
        }

        $manager->flush();
    }
}
