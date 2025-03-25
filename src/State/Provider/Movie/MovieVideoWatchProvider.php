<?php

declare(strict_types=1);

namespace App\State\Provider\Movie;

use App\Enum\MediaTypeEnum;
use App\State\Provider\AbstractVideoWatchProvider;

final class MovieVideoWatchProvider extends AbstractVideoWatchProvider
{
    protected function getMediaType(): MediaTypeEnum
    {
        return MediaTypeEnum::MOVIE;
    }
}
