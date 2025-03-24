<?php

declare(strict_types=1);

namespace App\DTO;

class Results
{
    public int $id;

    /**
     * @var TvShowResult[]
     */
    public array $results = [];
    public int $total_pages;
    public int $total_results;
}
