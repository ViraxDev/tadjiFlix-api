<?php

declare(strict_types=1);

namespace App\Enum\Exception;

use Symfony\Component\HttpFoundation\Response;

enum ErrorCodeEnum: string
{
    case INVALID_OR_EXPIRED_TOKEN = 'INVALID_OR_EXPIRED_TOKEN';

    public function getHttpStatusCode(): int
    {
        return match ($this) {
            self::INVALID_OR_EXPIRED_TOKEN => Response::HTTP_NOT_FOUND,
        };
    }

    public function getMessage(): string
    {
        return match ($this) {
            self::INVALID_OR_EXPIRED_TOKEN => 'Invalid or expired token.',
        };
    }
}
