<?php
declare(strict_types=1);

namespace App\DTO;

final class MovieResult
{
    public int $id;
    public ?string $backdrop_path;
    public string $title;
    public string $original_title;
    public string $overview;
    public ?string $poster_path;
    public string $media_type;
    public bool $adult;
    public string $original_language;
    public array $genre_ids;
    public float $popularity;
    public string $release_date;
    public bool $video;
    public float $vote_average;
    public int $vote_count;
}
