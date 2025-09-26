<?php

namespace App\Filter;

use ApiPlatform\Doctrine\Orm\Filter\AbstractFilter;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\PropertyInfo\Type;

class SearchFilter extends AbstractFilter
{
    protected function filterProperty(string $property, $value, QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, Operation $operation = null, array $context = []): void
    {
        if ($property !== 'search' || empty($value)) {
            return;
        }

        $alias = $queryBuilder->getRootAliases()[0];
        $parameterName = $queryNameGenerator->generateParameterName('search');

        if ($resourceClass === 'App\Entity\Auteur') {
            // Recherche dans nom, prenom ou nom complet pour les auteurs
            $queryBuilder
                ->andWhere(
                    $queryBuilder->expr()->orX(
                        $queryBuilder->expr()->like("LOWER({$alias}.nom)", "LOWER(:{$parameterName})"),
                        $queryBuilder->expr()->like("LOWER({$alias}.prenom)", "LOWER(:{$parameterName})"),
                        $queryBuilder->expr()->like("LOWER(CONCAT({$alias}.prenom, ' ', {$alias}.nom))", "LOWER(:{$parameterName})")
                    )
                )
                ->setParameter($parameterName, '%' . $value . '%');
        } elseif ($resourceClass === 'App\Entity\Categorie') {
            // Recherche dans nom et description pour les catégories
            $queryBuilder
                ->andWhere(
                    $queryBuilder->expr()->orX(
                        $queryBuilder->expr()->like("LOWER({$alias}.nom)", "LOWER(:{$parameterName})"),
                        $queryBuilder->expr()->like("LOWER({$alias}.description)", "LOWER(:{$parameterName})")
                    )
                )
                ->setParameter($parameterName, '%' . $value . '%');
        }
    }

    public function getDescription(string $resourceClass): array
    {
        return [
            'search' => [
                'property' => 'search',
                'type' => Type::BUILTIN_TYPE_STRING,
                'required' => false,
                'description' => 'Recherche dans les champs nom, prénom (auteurs) ou nom, description (catégories)',
            ],
        ];
    }
}
