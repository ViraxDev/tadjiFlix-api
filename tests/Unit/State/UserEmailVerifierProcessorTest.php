<?php

declare(strict_types=1);

namespace App\Tests\Unit\State;

use ApiPlatform\Metadata\Operation;
use App\DTO\EmailVerification;
use App\Entity\User;
use App\Exception\InvalidTokenException;
use App\State\Factory\UserFactoryInterface;
use App\State\UserEmailVerifierProcessor;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class UserEmailVerifierProcessorTest extends TestCase
{
    private MockObject $entityManager;
    private MockObject $repository;
    private MockObject $userFactory;

    /**
     * @var UserEmailVerifierProcessor<EmailVerification, User>
     */
    private UserEmailVerifierProcessor $processor;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(EntityRepository::class);
        $this->userFactory = $this->createMock(UserFactoryInterface::class);

        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->entityManager
            ->method('getRepository')
            ->with(User::class)
            ->willReturn($this->repository);

        $this->processor = new UserEmailVerifierProcessor($this->entityManager, $this->userFactory);
    }

    public function testProcessWithValidToken(): void
    {
        $token = 'valid-token';
        $dto = new EmailVerification($token);

        $user = $this->createMock(User::class);
        $user->expects($this->once())
            ->method('getVerificationTokenExpiresAt')
            ->willReturn(new \DateTimeImmutable('+1 hour'));

        $this->userFactory->expects($this->once())
            ->method('markAsVerified')
            ->with($user);

        $this->repository
            ->method('findOneBy')
            ->with(['verificationToken' => $token])
            ->willReturn($user);

        $this->entityManager
            ->expects($this->once())
            ->method('flush');

        $operation = $this->createMock(Operation::class);

        $result = $this->processor->process($dto, $operation);

        $this->assertSame($user, $result);
    }

    public function testProcessWithExpiredToken(): void
    {
        $token = 'expired-token';
        $dto = new EmailVerification($token);

        $user = $this->createMock(User::class);
        $user->expects($this->once())
            ->method('getVerificationTokenExpiresAt')
            ->willReturn(new \DateTimeImmutable('-1 hour'));

        $this->repository
            ->method('findOneBy')
            ->with(['verificationToken' => $token])
            ->willReturn($user);

        $this->userFactory
            ->expects($this->never())
            ->method('markAsVerified')
            ->with($user);

        $operation = $this->createMock(Operation::class);

        $this->expectException(InvalidTokenException::class);

        $this->processor->process($dto, $operation);
    }

    public function testProcessWithInvalidToken(): void
    {
        $token = 'invalid-token';
        $dto = new EmailVerification($token);

        $this->repository
            ->method('findOneBy')
            ->with(['verificationToken' => $token])
            ->willReturn(null);

        $this->userFactory
            ->expects($this->never())
            ->method('markAsVerified');

        $operation = $this->createMock(Operation::class);

        $this->expectException(InvalidTokenException::class);

        $this->processor->process($dto, $operation);
    }
}
