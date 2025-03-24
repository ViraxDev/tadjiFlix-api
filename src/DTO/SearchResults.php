<?php

declare(strict_types=1);

namespace App\DTO;

class SearchResults
{
    public int $page;

    /**
     * @var TvShowResult[]
     */
    public array $results = [];
    public int $total_pages;
    public int $total_results;
}
