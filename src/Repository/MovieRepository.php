<?php

namespace App\Repository;

use App\Entity\Movie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Repository for managing Movie entities, providing custom query methods.
 */
class MovieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Movie::class);
    }

    /**
     * Retrieves all movies with optional sorting.
     *
     * @return Movie[]
     */
    public function findAllMovies(array $order): array
    {
        $qb = $this->createQueryBuilder('m');

        foreach ($order as $field => $direction) {
            if (in_array($field, ['title', 'id']) && in_array(strtoupper($direction), ['ASC', 'DESC'])) {
                $qb->addOrderBy("m.$field", strtoupper($direction));
            }
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * Retrieves a specified number of random movies.
     *
     * @return Movie[]
     */
    public function findRandomMovies(int $itemsPerPage = 3): array
    {
        // Using native SQL query because Doctrine DQL does not support RAND() by default
        $conn = $this->getEntityManager()->getConnection();
        $sql = 'SELECT * FROM movie ORDER BY RAND() LIMIT :limit';
        $stmt = $conn->prepare($sql);
        $stmt->bindValue('limit', $itemsPerPage, \PDO::PARAM_INT);
        $result = $stmt->executeQuery();

        return $result->fetchAllAssociative();
    }

    /**
     * Retrieves movies starting with a given letter, with pagination and sorting, applying special logic for 'W'.
     *
     * @return Movie[]
     */
    public function findByLetter(string $letter, int $page, int $itemsPerPage, array $order): array
    {
        // Using native SQL query because Doctrine DQL does not support % operator or MOD function by default
        // and I decided this is the appropriate place for this logic
        $conn = $this->getEntityManager()->getConnection();
        $sql = 'SELECT * FROM movie WHERE LOWER(title) LIKE :letter';
        $params = ['letter' => strtolower($letter) . '%'];

        if (strtolower($letter) === 'w') {
            $sql .= ' AND MOD(CHAR_LENGTH(title), 2) = 0';
        }

        $sql .= $this->getOrderBySql($order);
        $sql .= ' LIMIT :limit OFFSET :offset';
        $params['limit'] = $itemsPerPage;
        $params['offset'] = ($page - 1) * $itemsPerPage;

        $stmt = $conn->prepare($sql);
        $stmt->bindValue('letter', $params['letter']);
        $stmt->bindValue('limit', $params['limit'], \PDO::PARAM_INT);
        $stmt->bindValue('offset', $params['offset'], \PDO::PARAM_INT);

        $result = $stmt->executeQuery();

        return $result->fetchAllAssociative();
    }

    /**
     * Generates ORDER BY clause for SQL query based on allowed fields and directions.
     */
    private function getOrderBySql(array $order, array $allowedFields = ['title']): string
    {
        $orderBy = [];
        foreach ($order as $field => $direction) {
            if (in_array($field, $allowedFields) && in_array(strtoupper($direction), ['ASC', 'DESC'])) {
                $orderBy[] = "$field " . strtoupper($direction);
            }
        }

        return $orderBy ? ' ORDER BY ' . implode(', ', $orderBy) : '';
    }
}
