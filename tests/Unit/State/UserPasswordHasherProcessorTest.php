<?php

declare(strict_types=1);

namespace App\Tests\Unit\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\User;
use App\Service\EmailVerification\EmailVerificationServiceInterface;
use App\State\Factory\UserFactoryInterface;
use App\State\UserPasswordHasherProcessor;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class UserPasswordHasherProcessorTest extends TestCase
{
    private MockObject $persistProcessor;
    private MockObject $userFactory;
    private MockObject $emailVerificationService;

    /**
     * @var UserPasswordHasherProcessor<User, User>
     */
    private UserPasswordHasherProcessor $userPasswordHasher;

    protected function setUp(): void
    {
        $this->persistProcessor = $this->createMock(ProcessorInterface::class);
        $this->userFactory = $this->createMock(UserFactoryInterface::class);
        $this->emailVerificationService = $this->createMock(EmailVerificationServiceInterface::class);

        $this->userPasswordHasher = new UserPasswordHasherProcessor(
            $this->persistProcessor,
            $this->emailVerificationService,
            $this->userFactory,
        );
    }

    #[DataProvider('getUser')]
    public function testProcessWithUserWithoutPlainPassword(User $user): void
    {
        $operation = $this->createMock(Operation::class);
        $user->eraseCredentials();

        $this->persistProcessor
            ->expects($this->once())
            ->method('process')
            ->with($user, $operation, [], [])
            ->willReturn($user);

        $this->userFactory->expects($this->never())->method('setupInitialUser');

        $result = $this->userPasswordHasher->process($user, $operation);

        $this->assertSame($user, $result);
    }

    #[DataProvider('getUser')]
    public function testProcessWithValidUserData(User $user): void
    {
        $operation = $this->createMock(Operation::class);

        $this->userFactory->expects($this->once())
            ->method('setupInitialUser')
            ->with($user);

        $this->emailVerificationService
            ->expects($this->once())
            ->method('sendVerificationEmail')
            ->with($user)
        ;

        $this->persistProcessor
            ->expects($this->once())
            ->method('process')
            ->with($user, $operation, [], [])
            ->willReturn($user);

        $result = $this->userPasswordHasher->process($user, $operation);

        $this->assertSame($user, $result);
        $this->assertEquals(['ROLE_USER'], $result->getRoles());
        $this->assertFalse($result->isVerified());
    }

    #[DataProvider('getUser')]
    public function testProcessWithCustomContext(User $user): void
    {
        $operation = $this->createMock(Operation::class);
        $context = ['groups' => ['user:write']];
        $uriVariables = ['id' => 1];

        $this->emailVerificationService
            ->expects($this->once())
            ->method('sendVerificationEmail')
            ->with($user)
        ;

        $this->persistProcessor
            ->expects($this->once())
            ->method('process')
            ->with(
                $this->isInstanceOf(User::class),
                $operation,
                $uriVariables,
                $context
            )
            ->willReturn($user);

        $result = $this->userPasswordHasher->process($user, $operation, $uriVariables, $context);

        $this->assertSame($user, $result);
    }

    /**
     * @return array<int, array<int, User>>
     */
    public static function getUser(): array
    {
        return [[(new User())->setPlainPassword('password123')]];
    }
}
