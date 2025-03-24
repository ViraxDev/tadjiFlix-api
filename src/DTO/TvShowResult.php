<?php
declare(strict_types=1);

namespace App\DTO;

final class TvShowResult
{
    public int $id;
    public string $name;
    public string $overview;
    public ?string $first_air_date;
    public string $original_name;
    public string $original_language;
    public bool $adult;
    public ?string $backdrop_path;
    public array $created_by;
    public array $episode_run_time;
    public array $genres;
    public array $languages;
    public array $networks;
    public array $origin_country;
    public float $popularity;
    public ?string $poster_path;
    public array $production_companies;
    public array $production_countries;
    public array $seasons;
    public array $spoken_languages;
    public float $vote_average;
    public int $vote_count;
    public array $translations;

    /** @var ?int[] $genre_ids[] */
    public array $genre_ids;
}
