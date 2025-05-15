# Movie Recommendation

Backend application built with Symfony and API Platform to provide movie recommendations based on three algorithms.

## Requirements

- Docker
- Docker Compose

## Setup

1. Clone the repository:

   ```bash
   git clone git@github.com:mikowhyosowski/movie-recommendation.git
   cd movie-recommendation
   ```
2. Start Docker containers:

   ```bash
   docker-compose up -d
   ```
3. Install dependencies:

   ```bash
   docker-compose exec php composer install
   ```
4. Create database:

   ```bash
   docker-compose exec php php bin/console doctrine:database:create
   ```
5. Run migrations:

   ```bash
   docker-compose exec php php bin/console doctrine:migrations:migrate
   ```
6. Load fixtures:

   ```bash
   docker-compose exec php php bin/console doctrine:fixtures:load
   ```
7. Access the API:
   - API documentation: `http://localhost:8000/api`
   - Example endpoint: 
     - `curl http://localhost:8000/api/movies/recommend/random`
     - `curl http://localhost:8000/api/movies/recommend/by-letter?letter=W`
     - `curl http://localhost:8000/api/movies/recommend/multi-word`
8. Run tests:

   ```bash
   docker-compose exec php vendor/bin/phpunit
   ```

## Endpoints

- `GET /api/movies` - List all movies
- `GET /api/movies/{id}` - Return movie by id
- `GET /api/movies/recommend/random` - Returns 3 random movies
- `GET /api/movies/recommend/by-letter` - Returns movies starting with the given letter (for 'W', only even-length titles are returned) (supports filtering, e.g., `?letter=W`)
- `GET /api/movies/recommend/multi-word` - Returns movies with multi-word titles

## Notes

- The `by-letter` endpoint is generalized to support any letter with a special condition for 'W' as per task requirements.
- Duplicate movie titles in `movies.php` are removed using `array_unique` (pending confirmation from the hiring team).
