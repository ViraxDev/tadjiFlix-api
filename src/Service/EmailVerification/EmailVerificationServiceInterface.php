<?php

declare(strict_types=1);

namespace App\Service\EmailVerification;

use App\Entity\User;

interface EmailVerificationServiceInterface
{
    public function sendVerificationEmail(User $user): void;
}
