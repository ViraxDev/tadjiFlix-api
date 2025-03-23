<?php

declare(strict_types=1);

namespace App\Service;

final class UserService
{
    public static function generateVerificationToken(): string
    {
        return bin2hex(random_bytes(32));
    }
}
