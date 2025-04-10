<?php

declare(strict_types=1);

namespace App\DTO;

final class TvShowSearchResults extends SearchResults
{

    /**
     * @var TvShowResult[]
     */
    public array $results = [];
}
