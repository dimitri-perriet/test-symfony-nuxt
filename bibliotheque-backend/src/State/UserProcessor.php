<?php

namespace App\State;

use ApiPlatform\Doctrine\Common\State\PersistProcessor;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\User;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserProcessor implements ProcessorInterface
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher,
        private PersistProcessor $persistProcessor
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        if (!$data instanceof User) {
            $this->persistProcessor->process($data, $operation, $uriVariables, $context);
            return;
        }

        // Hasher le mot de passe si plainPassword est fourni
        $plainPassword = $data->getPlainPassword();
        if ($plainPassword) {
            $hashedPassword = $this->passwordHasher->hashPassword($data, $plainPassword);
            $data->setPassword($hashedPassword);
            $data->setPlainPassword(null); // Nettoyer le plainPassword pour la sécurité
        }

        try {
            $this->persistProcessor->process($data, $operation, $uriVariables, $context);
        } catch (UniqueConstraintViolationException $e) {
            // Gérer spécifiquement les violations de contrainte d'unicité
            if (str_contains($e->getMessage(), 'uniq_identifier_email')) {
                throw new ConflictHttpException('Cet email est déjà utilisé');
            }
            throw $e;
        }
    }
}
