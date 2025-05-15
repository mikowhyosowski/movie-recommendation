<?php

namespace Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RecommendationApiTest extends WebTestCase
{
    public function testGetRandomMovies(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/movies/recommend/random');

        $this->assertResponseIsSuccessful();
        $data = json_decode($client->getResponse()->getContent(), true);

        $movies = $data['hydra:member'] ?? $data;
        $this->assertCount(3, $movies);
    }

    public function testGetMoviesByLetterW(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/movies/recommend/by-letter?letter=W');

        $this->assertResponseIsSuccessful();
        $data = json_decode($client->getResponse()->getContent(), true);
        $movies = $data['hydra:member'] ?? $data;

        foreach ($movies as $movie) {
            $this->assertStringStartsWith('W', $movie['title'], true);
            $this->assertSame(0, mb_strlen($movie['title']) % 2);            
        }
    }

    public function testGetMoviesByLetterOther(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/movies/recommend/by-letter?letter=M');

        $this->assertResponseIsSuccessful();
        $data = json_decode($client->getResponse()->getContent(), true);
        $movies = $data['hydra:member'] ?? $data;

        foreach ($movies as $movie) {
            $this->assertStringStartsWith('M', $movie['title'], true);
        }
    }

    public function testGetMoviesByLetterInvalid(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/movies/recommend/by-letter?letter=12');

        $this->assertResponseStatusCodeSame(400);
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertSame('Letter must be a single alphabetic character', $data['error']);
    }

    public function testGetMultiWordTitles(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/movies/recommend/multi-word');

        $this->assertResponseIsSuccessful();
        $data = json_decode($client->getResponse()->getContent(), true);
        $movies = $data['hydra:member'] ?? $data;

        foreach ($movies as $movie) {
            $this->assertGreaterThan(1, count(explode(' ', trim($movie['title']))));
        }
    }
}
