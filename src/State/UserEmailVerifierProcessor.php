<?php

declare(strict_types=1);

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\DTO\EmailVerification;
use App\Entity\User;
use App\Exception\InvalidTokenException;
use App\State\Factory\UserFactoryInterface;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @template T1 of EmailVerification
 * @template T2 of User
 *
 * @implements ProcessorInterface<T1, T2>
 */
final readonly class UserEmailVerifierProcessor implements ProcessorInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserFactoryInterface $userFactory,
    ) {
    }

    /**
     * @param EmailVerification $data
     * @param array<string, mixed> $context
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy([
            'verificationToken' => $data->token,
        ]);

        if (!$user || $user->getVerificationTokenExpiresAt() < new \DateTime()) {
            throw new InvalidTokenException();
        }

        $this->userFactory->markAsVerified($user);
        $this->entityManager->flush();

        return $user;
    }
}
