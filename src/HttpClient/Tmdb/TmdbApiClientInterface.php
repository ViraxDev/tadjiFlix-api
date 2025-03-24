<?php

declare(strict_types=1);

namespace App\HttpClient\Tmdb;

use App\Enum\MediaTypeEnum;

interface TmdbApiClientInterface
{
    public function getTrendingTvShows(
        string $timeWindow = 'week',
        string $language = 'en-US',
        int $page = 1
    ): array;

    public function getTrendingMovies(
        string $timeWindow = 'week',
        string $language = 'en-US',
        int $page = 1
    ): array;

    public function getTvShowTranslations(int $id): array;
    public function getMovieTranslations(int $id): array;
    public function getTvShowDetails(int $tvShowId, ?string $language = null): array;
    public function getMovieDetails(int $id, ?string $language = null): array;
    public function searchTvShows(
        string $query,
        ?int $firstAirDateYear = null,
        bool $includeAdult = false,
        string $language = 'en-US',
        int $page = 1
    ): array;
    public function searchMovies(
        string $query,
        ?string $primaryReleaseYear = null,
        bool $includeAdult = false,
        string $language = 'en-US',
        int $page = 1
    ): array;

    public function getVideos(MediaTypeEnum $mediaType, int $id, string $language = 'en-US'): array;
}
