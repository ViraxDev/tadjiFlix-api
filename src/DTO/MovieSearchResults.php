<?php

declare(strict_types=1);

namespace App\DTO;

final class MovieSearchResults extends SearchResults
{

    /**
     * @var MovieResult[]
     */
    public array $results = [];
}
