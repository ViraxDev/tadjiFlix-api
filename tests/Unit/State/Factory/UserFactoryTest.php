<?php

declare(strict_types=1);

namespace App\Tests\Unit\State\Factory;

use App\Entity\User;
use App\Security\Voter\VerifiedUserVoter;
use App\State\Factory\UserFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class UserFactoryTest extends TestCase
{
    private MockObject $passwordHasher;
    private UserFactory $userFactory;

    protected function setUp(): void
    {
        $this->passwordHasher = $this->createMock(UserPasswordHasherInterface::class);
        $this->userFactory = new UserFactory($this->passwordHasher);
    }

    public function testMarkAsVerified(): void
    {
        $user = new User();
        $user->setVerificationToken('token');
        $user->setVerificationTokenExpiresAt(new \DateTimeImmutable());
        $user->setVerified(false);
        $user->setRoles(['ROLE_USER']);

        $verifiedUser = $this->userFactory->markAsVerified($user);

        self::assertTrue($verifiedUser->isVerified());
        self::assertNull($verifiedUser->getVerificationToken());
        self::assertNull($verifiedUser->getVerificationTokenExpiresAt());
        self::assertContains(VerifiedUserVoter::VERIFIED, $verifiedUser->getRoles());
    }

    public function testSetupInitialUser(): void
    {
        $plainPassword = 'password123';
        $hashedPassword = 'hashed_password';

        $user = new User();
        $user->setPlainPassword($plainPassword);

        $this->passwordHasher
            ->expects(self::once())
            ->method('hashPassword')
            ->with($user, $plainPassword)
            ->willReturn($hashedPassword);

        $initializedUser = $this->userFactory->setupInitialUser($user);

        self::assertSame(['ROLE_USER'], $initializedUser->getRoles());
        self::assertSame($hashedPassword, $initializedUser->getPassword());
        self::assertFalse($initializedUser->isVerified());
        self::assertNull($initializedUser->getPlainPassword(), 'Plain password should be erased');
    }
}
