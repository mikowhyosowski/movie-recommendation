<?php

namespace App\Controller;

use App\Service\RecommendationService;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;

class RecommendationController
{
    public function __construct(
        private RecommendationService $recommendationService,
        private SerializerInterface $serializer
    ) {}

    public function getRandomMovies(Request $request): JsonResponse
    {
        $itemsPerPage = (int) $request->query->get('itemsPerPage', 3);

        $movies = $this->recommendationService->getRandomMovies($itemsPerPage);

        return new JsonResponse($this->serializer->serialize($movies, 'json', ['groups' => 'movie:read']), 200, [], true);
    }

    public function getMoviesByLetter(Request $request): JsonResponse
    {
        $letter = (string) $request->query->get('letter', '');
        $page = (int) $request->query->get('page', 1);
        $itemsPerPage = (int) $request->query->get('itemsPerPage', 10);
        $order = $request->query->all('order');

        if ($page < 1 || $itemsPerPage < 1) {
            return new JsonResponse(['error' => 'Page and itemsPerPage must be positive integers'], 400);
        }

        if (!preg_match('/^[a-zA-Z]$/', $letter)) {
            return new JsonResponse(['error' => 'Letter must be a single alphabetic character'], 400);
        }
        $movies = $this->recommendationService->getMoviesByLetter($letter, $page, $itemsPerPage, $order);

        return new JsonResponse($this->serializer->serialize($movies, 'json', ['groups' => 'movie:read']), 200, [], true);
    }

    public function getMultiWordTitles(Request $request): JsonResponse
    {
        $order = $request->query->all('order');

        $movies = $this->recommendationService->getMultiWordTitles($order);

        return new JsonResponse($this->serializer->serialize($movies, 'json', ['groups' => 'movie:read']), 200, [], true);
    }
}