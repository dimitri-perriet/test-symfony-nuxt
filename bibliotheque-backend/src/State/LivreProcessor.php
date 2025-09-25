<?php

namespace App\State;

use ApiPlatform\Doctrine\Common\State\PersistProcessor;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Livre;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;

/**
 * Processor pour automatiquement assigner le propriétaire lors de la création d'un livre
 */
final class LivreProcessor implements ProcessorInterface
{
    public function __construct(
        private PersistProcessor $persistProcessor,
        private Security $security,
        private EntityManagerInterface $entityManager
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        if ($data instanceof Livre) {
            if ($operation instanceof \ApiPlatform\Metadata\Post) {
                // Auto-assigner le propriétaire lors de la création
                $data->setProprietaire($this->security->getUser());
            } elseif ($operation instanceof \ApiPlatform\Metadata\Put) {
                // Pour les PUT, récupérer le propriétaire existant
                $id = $uriVariables['id'] ?? null;
                if ($id) {
                    $existingLivre = $this->entityManager->find(Livre::class, $id);
                    if ($existingLivre && !$data->getProprietaire()) {
                        $data->setProprietaire($existingLivre->getProprietaire());
                    }
                }
            }
        }

        return $this->persistProcessor->process($data, $operation, $uriVariables, $context);
    }
}
