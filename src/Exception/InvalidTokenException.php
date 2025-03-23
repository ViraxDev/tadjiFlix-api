<?php

declare(strict_types=1);

namespace App\Exception;

use App\Enum\Exception\ErrorCodeEnum;

final class InvalidTokenException extends AbstractApiException
{
    public function getErrorCodeEnum(): ErrorCodeEnum
    {
        return ErrorCodeEnum::INVALID_OR_EXPIRED_TOKEN;
    }
}
