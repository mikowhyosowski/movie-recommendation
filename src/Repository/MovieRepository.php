<?php

namespace App\Repository;

use App\Entity\Movie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Movie>
 */
class MovieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Movie::class);
    }

    /**
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
     * @return Movie[]
     */
    public function findRandomMovies(int $itemsPerPage = 3): array
    {
        // Napisałem natywne zapytanie SQL ze względu na to, że 
        // Doctrine DQL nie obsługuje funkcji RAND() domyślnie
        $conn = $this->getEntityManager()->getConnection();
        $sql = 'SELECT * FROM movie ORDER BY RAND() LIMIT :limit';
        $stmt = $conn->prepare($sql);
        $stmt->bindValue('limit', $itemsPerPage, \PDO::PARAM_INT);
        $result = $stmt->executeQuery();

        return $result->fetchAllAssociative();
    }

    /**
     * @return Movie[]
     */
    public function findByLetter(string $letter, int $page, int $itemsPerPage, array $order): array
    {
        // Napisałem natywne zapytanie SQL ze względu na to, że 
        // Doctrine DQL nie obsługuje operatora % ani funkcji MOD domyślnie
        // a uznałem, że to tutaj powinno się zadziewać
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
