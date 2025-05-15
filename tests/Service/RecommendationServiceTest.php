<?php

namespace Tests\Service;

use App\Entity\Movie;
use App\Repository\MovieRepository;
use App\Service\RecommendationService;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class RecommendationServiceTest extends TestCase
{
    private RecommendationService $service;
    private MovieRepository|MockObject $movieRepository;

    protected function setUp(): void
    {
        $this->movieRepository = $this->createMock(MovieRepository::class);
        $this->service = new RecommendationService($this->movieRepository);
    }

    public function testGetRandomMoviesReturnsThreeTitles(): void
    {
        $movies = [
            (new Movie())->setTitle('Pulp Fiction'),
            (new Movie())->setTitle('Incepcja'),
            (new Movie())->setTitle('Matrix'),
            (new Movie())->setTitle('Siedem'),
        ];

        $this->movieRepository
            ->method('findRandomMovies')
            ->with(3)
            ->willReturn(array_slice($movies, 0, 3));

        $result = $this->service->getRandomMovies(3);
        $this->assertCount(3, $result);
        $this->assertContainsOnlyInstancesOf(Movie::class, $result);
    }

    public function testGetMoviesByLetterW(): void
    {
        $movies = [
            (new Movie())->setTitle('Harry Potter'),
            (new Movie())->setTitle('Whiplash'),
            (new Movie())->setTitle('Wilk'),
        ];

        $this->movieRepository
            ->method('findByLetter')
            ->with('W', 1, 10, [])
            ->willReturn($movies);

        $result = $this->service->getMoviesByLetter('W', 1, 10, []);
        $titles = array_map(fn(Movie $movie) => $movie->getTitle(), $result);
        $this->assertSame(['Harry Potter', 'Whiplash', 'Wilk'], $titles);
    }

    public function testGetMoviesByLetterOther(): void
    {
        $movies = [
            (new Movie())->setTitle('Matrix'),
            (new Movie())->setTitle('Mad Max'),
        ];

        $this->movieRepository
            ->method('findByLetter')
            ->with('M', 1, 10, [])
            ->willReturn($movies);

        $result = $this->service->getMoviesByLetter('M', 1, 10, []);
        $titles = array_map(fn(Movie $movie) => $movie->getTitle(), $result);
        $this->assertSame(['Matrix', 'Mad Max'], $titles);
    }

    public function testGetMultiWordTitles(): void
    {
        $movies = [
            (new Movie())->setTitle('Pulp Fiction'),
            (new Movie())->setTitle('Matrix'),
            (new Movie())->setTitle('Władca Pierścieni'),
        ];

        $this->movieRepository
            ->method('findAllMovies')
            ->with([])
            ->willReturn($movies);

        $result = $this->service->getMultiWordTitles([]);
        $titles = array_map(fn(Movie $movie) => $movie->getTitle(), $result);
        $this->assertSame(['Pulp Fiction', 'Władca Pierścieni'], $titles);
    }
}
