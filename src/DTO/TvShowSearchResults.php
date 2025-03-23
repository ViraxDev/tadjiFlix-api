<?php

declare(strict_types=1);

namespace App\DTO;

final class TvShowSearchResults
{
    public int $page;
    public array $results = [];
    public int $total_pages;
    public int $total_results;
}
