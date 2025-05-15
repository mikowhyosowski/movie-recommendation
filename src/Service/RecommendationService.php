<?php

namespace App\Service;

use App\Entity\Movie;
use App\Repository\MovieRepository;

/**
 * Service for generating movie recommendations based on various criteria.
 */
class RecommendationService
{
    public function __construct(private MovieRepository $movieRepository)
    {
    }

    /**
     * Retrieves a specified number of random movies.
     *
     * @param int $itemsPerPage Number of random movies to retrieve (default: 3)
     * @return Movie[] Array of randomly selected movies
     */
    public function getRandomMovies(int $itemsPerPage): array
    {
        $movies = $this->movieRepository->findRandomMovies($itemsPerPage);

        return $movies;
    }

    /**
     * Retrieves movies starting with the specified letter, with pagination and sorting.
     * For letter 'W', only movies with even-length titles are returned.
     *
     * @param string $letter The letter to filter movie titles by (single alphabetic character)
     * @param int $page The page number for pagination
     * @param int $itemsPerPage Number of items per page
     * @param array $order Sorting criteria (e.g., ['title' => 'ASC'])
     * @return Movie[] Array of filtered movies
     */
    public function getMoviesByLetter(string $letter, int $page, int $itemsPerPage, array $order = []): array
    {
        $movies = $this->movieRepository->findByLetter($letter, $page, $itemsPerPage, $order);

        return $movies;
    }

    /**
     * Retrieves movies with titles containing more than one word, with optional sorting.
     *
     * @param array $order Sorting criteria (e.g., ['title' => 'ASC'])
     * @return Movie[] Array of movies with multi-word titles
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
