<?php

namespace App\Filter;

use ApiPlatform\Doctrine\Orm\Filter\FilterInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use Doctrine\ORM\QueryBuilder;

class LetterFilter implements FilterInterface
{
    public function apply(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        ?Operation $operation = null,
        array $context = []
    ): void {
        // Pobierz parametr letter z kontekstu (z URL-a)
        $letter = $context['uri_variables']['letter'] ?? null;

        if (!$letter) {
            return;
        }

        // Dodaj warunek do zapytania
        $alias = $queryBuilder->getRootAliases()[0];
        $queryBuilder
            ->andWhere(sprintf('%s.title LIKE :letter', $alias))
            ->setParameter('letter', strtoupper($letter) . '%');
    }

    public function getDescription(string $resourceClass): array
    {
        return [
            'letter' => [
                'property' => null, // Nie wiążemy z konkretną właściwością encji
                'type' => 'string',
                'required' => true,
                'description' => 'Filter movies by the first letter of the title (e.g., "W" for movies starting with W)',
                'openapi' => [
                    'example' => 'W',
                    'maxLength' => 1
                ]
            ]
        ];
    }
}
