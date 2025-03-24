<?php
declare(strict_types=1);

namespace App\DTO;

final class TvShow
{
    public int $id;
    public ?string $name = null;
    public ?string $overview = null;
    public ?string $first_air_date = null;
    public ?string $original_name = null;
    public ?string $original_language = null;
    public ?bool $adult = null;
    public ?string $backdrop_path = null;
    public ?array $created_by = [];
    public ?array $episode_run_time = [];
    public ?array $genres = [];
    public ?string $homepage = null;
    public ?bool $in_production = null;
    public ?array $languages = [];
    public ?string $last_air_date = null;
    public ?array $last_episode_to_air = null;
    public ?array $next_episode_to_air = null;
    public ?array $networks = [];
    public ?int $number_of_episodes = null;
    public ?int $number_of_seasons = null;
    public ?array $origin_country = [];
    public ?float $popularity = null;
    public ?string $poster_path = null;
    public ?array $production_companies = [];
    public ?array $production_countries = [];
    public ?array $seasons = [];
    public ?array $spoken_languages = [];
    public ?string $status = null;
    public ?string $tagline = null;
    public ?string $type = null;
    public ?float $vote_average = null;
    public ?int $vote_count = null;

    public ?array $translations = [];
}
