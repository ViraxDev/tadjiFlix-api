<?php

declare(strict_types=1);

namespace App\Tests\Factory;

use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<User>
 */
final class UserFactory extends PersistentProxyObjectFactory
{
    public const string SECURED_DEFAULT_PASSWORD = 'kPDKGJldshfsdkj876836863!!!!!!';

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     */
    public function __construct(private readonly ?UserPasswordHasherInterface $passwordHasher = null)
    {
        parent::__construct();
    }

    public static function class(): string
    {
        return User::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     */
    protected function defaults(): array|callable
    {
        return [
            'email' => self::faker()->unique()->email(),
            'password' => self::faker()->password(),
            'roles' => ['ROLE_ADMIN'],
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this
            ->afterInstantiate(function (User $user) {
                if (null !== $this->passwordHasher) {
                    $user->setPassword($this->passwordHasher->hashPassword($user, self::SECURED_DEFAULT_PASSWORD));
                }
            })
        ;
    }
}
