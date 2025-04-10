<?php

declare(strict_types=1);

namespace App\DTO;

final class VideoResults extends SearchResults
{

    /**
     * @var VideoResult[]
     */
    public array $results = [];
}
