<?php
declare(strict_types=1);

namespace App\DTO;

final class Movie
{
    public bool $adult;
    public ?string $backdrop_path;
    public ?object $belongs_to_collection;
    public int $budget;
    public array $genres;
    public string $homepage;
    public int $id;
    public ?string $imdb_id;
    public array $origin_country;
    public string $original_language;
    public string $original_title;
    public string $overview;
    public float $popularity;
    public ?string $poster_path;
    public array $production_companies;
    public array $production_countries;
    public string $release_date;
    public int $revenue;
    public int $runtime;
    public array $spoken_languages;
    public string $status;
    public string $tagline;
    public string $title;
    public bool $video;
    public float $vote_average;
    public int $vote_count;
    public array $translations = [];
}