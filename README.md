# Movie Recommendation

Simple backend application built with Symfony to provide movie recommendations based on three algorithms.

## Requirements
- PHP 8.1+
- Composer
- Symfony CLI

## Setup
1. Clone the repository: `git clone git@github.com:mikowhyosowski/movie-recommendation.git`
2. Install dependencies: `composer install`
3. Start the server: `symfony server:start`
4. Run tests: `vendor/bin/phpunit`

## Endpoints
- `GET /api/movies/recommend/random` - Returns 3 random movie titles.
- `GET /api/movies/recommend/letter-w` - Returns movies starting with 'W' with even title length.
- `GET /api/movies/recommend/multi-word` - Returns movie titles with more than one word.

## Tests
Unit tests for `MoviesRecommendationService` and functional tests for API endpoints are located in the `tests/` directory.
