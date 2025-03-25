<?php

declare(strict_types=1);

namespace App\State\Provider\TvShow;

use App\Enum\MediaTypeEnum;
use App\State\Provider\AbstractVideoWatchProvider;

final class TvShowVideoWatchProvider extends AbstractVideoWatchProvider
{
    protected function getMediaType(): MediaTypeEnum
    {
        return MediaTypeEnum::TV_SHOW;
    }
}
