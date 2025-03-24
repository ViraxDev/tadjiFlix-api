<?php

declare(strict_types=1);

namespace App\State\Provider\Movie;

use App\DTO\VideoResults;
use App\Enum\MediaTypeEnum;
use App\State\Provider\AbstractVideoProvider;

final class MovieVideoProvider extends AbstractVideoProvider
{
    protected function getMediaType(): MediaTypeEnum
    {
        return MediaTypeEnum::MOVIE;
    }
}
