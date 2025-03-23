<?php

declare(strict_types=1);

namespace App\Security\Voter;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * @template TAttribute of string
 * @template TSubject of User
 *
 * @extends Voter<TAttribute, TSubject>
 */
final class VerifiedUserVoter extends Voter
{
    public const string VERIFIED = 'ROLE_VERIFIED_USER';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return self::VERIFIED === $attribute && $subject instanceof User;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof User) {
            return false;
        }

        return $user->isVerified();
    }
}
