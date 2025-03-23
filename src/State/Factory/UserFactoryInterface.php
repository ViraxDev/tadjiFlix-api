<?php

declare(strict_types=1);

namespace App\State\Factory;

use App\Entity\User;

interface UserFactoryInterface
{
    public function markAsVerified(User $user): User;

    public function setupInitialUser(User $user): User;
}
