<?php

declare(strict_types=1);

namespace App\State\Provider\TvShow;

use App\Enum\MediaTypeEnum;
use App\State\Provider\AbstractVideoProvider;

final class TvShowVideoProvider extends AbstractVideoProvider
{
    protected function getMediaType(): MediaTypeEnum
    {
        return MediaTypeEnum::TV_SHOW;
    }
}
