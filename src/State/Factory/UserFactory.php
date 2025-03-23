<?php

declare(strict_types=1);

namespace App\State\Factory;

use App\Entity\User;
use App\Security\Voter\VerifiedUserVoter;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final readonly class UserFactory implements UserFactoryInterface
{
    public function __construct(private UserPasswordHasherInterface $passwordHasher)
    {
    }

    public function markAsVerified(User $user): User
    {
        return $user
            ->setVerified(true)
            ->setVerificationToken(null)
            ->setVerificationTokenExpiresAt(null)
            ->addRole(VerifiedUserVoter::VERIFIED);
    }

    public function setupInitialUser(User $user): User
    {
        $hashedPassword = $this->passwordHasher->hashPassword(
            $user,
            $user->getPlainPassword()
        );

        $user
            ->setRoles(['ROLE_USER'])
            ->setPassword($hashedPassword)
            ->setVerified(false)
            ->eraseCredentials();

        return $user;
    }
}
