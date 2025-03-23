<?php

declare(strict_types=1);

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\User;
use App\Service\EmailVerification\EmailVerificationServiceInterface;
use App\State\Factory\UserFactoryInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

/**
 * @template T1 of User
 * @template T2 of User
 *
 * @implements ProcessorInterface<T1, T2>
 */
final readonly class UserPasswordHasherProcessor implements ProcessorInterface
{
    /**
     * @param ProcessorInterface<User, User> $persistProcessor
     */
    public function __construct(
        #[Autowire(service: 'api_platform.doctrine.orm.state.persist_processor')]
        private ProcessorInterface $persistProcessor,
        private EmailVerificationServiceInterface $emailVerificationService,
        private UserFactoryInterface $userFactory,
    ) {
    }

    /**
     * @param User                 $data
     * @param array<string, mixed> $context
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        if (null === $data->getPlainPassword()) {
            return $this->persistProcessor->process($data, $operation, $uriVariables, $context);
        }

        $this->userFactory->setupInitialUser($data);

        $result = $this->persistProcessor->process($data, $operation, $uriVariables, $context);

        $this->emailVerificationService->sendVerificationEmail($data);

        return $result;
    }
}
