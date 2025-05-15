<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use App\Filter\LetterFilter;
use App\Repository\MovieRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Represents a Movie entity with API Platform and Doctrine ORM integration.
 */
#[ORM\Entity(repositoryClass: MovieRepository::class)]
#[ApiResource(
    operations: [
        new Get(),
        new GetCollection(
            filters: ['app.api_platform.filter.movie_order']
        )
    ],
    normalizationContext: ['groups' => ['movie:read']],
    paginationItemsPerPage: 10,
    paginationMaximumItemsPerPage: 50,
    paginationClientItemsPerPage: true
)]

#[GetCollection(
    uriTemplate: '/movies/recommend/random',
    controller: 'App\Controller\RecommendationController::getRandomMovies',
    description: 'Returns 3 (default) random movies',
    paginationItemsPerPage: 3,
    filters: [],
    name: 'recommend_random',
    normalizationContext: ['groups' => ['movie:read']]
)]
#[GetCollection(
    uriTemplate: '/movies/recommend/by-letter',
    controller: 'App\Controller\RecommendationController::getMoviesByLetter',
    description: 'Returns movies starting with a given letter (for W, only even-length titles are returned)',
    filters: [LetterFilter::class, 'app.api_platform.filter.movie_order'],
    name: 'recommend_by_letter',
    normalizationContext: ['groups' => ['movie:read']]
)]
#[GetCollection(
    uriTemplate: '/movies/recommend/multi-word',
    controller: 'App\Controller\RecommendationController::getMultiWordTitles',
    description: 'Returns movies with multi-word titles',
    paginationClientItemsPerPage: false,
    paginationEnabled: false,
    filters: ['app.api_platform.filter.movie_order'],
    name: 'recommend_multi_word',
    normalizationContext: ['groups' => ['movie:read']]
)]
class Movie
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['movie:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['movie:read'])]
    private ?string $title = null;

    /**
     * Gets the unique identifier of the movie.
     *
     * @return int|null The movie ID, or null if not set
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Gets the title of the movie.
     *
     * @return string|null The movie title, or null if not set
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * Sets the title of the movie.
     *
     * @param string $title The title to set
     * @return self Returns this instance for method chaining
     */
    public function setTitle(string $title): static
    {
        $this->title = $title;
        return $this;
    }
}
