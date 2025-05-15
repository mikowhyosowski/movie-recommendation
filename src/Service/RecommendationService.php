<?php

namespace App\Service;

use App\Entity\Movie;
use App\Repository\MovieRepository;

class RecommendationService
{
    public function __construct(private MovieRepository $movieRepository)
    {
    }

    /**
     * Returns 3 random movies.
     *
     * @return Movie[]
     */
    public function getRandomMovies(int $itemsPerPage): array
    {
        $movies = $this->movieRepository->findRandomMovies($itemsPerPage);

        return $movies;
    }

    /**
     * Returns movies starting with the given letter. For 'W', only even-length titles are returned.
     *
     * @return Movie[]
     */
    public function getMoviesByLetter(string $letter, int $page, int $itemsPerPage, array $order = []): array
    {
        $movies = $this->movieRepository->findByLetter($letter, $page, $itemsPerPage, $order);

        return $movies;
    }

    /**
     * Returns movies with more than one word in the title.
     *
     * @return Movie[]
     */
    public function getMultiWordTitles(array $order = []): array
    {
        $movies = $this->movieRepository->findAllMovies($order);

        return array_values(array_filter($movies, function (Movie $movie) {
            $title = $movie->getTitle();

            return count(explode(' ', trim($title))) > 1;
        }));
    }
}
