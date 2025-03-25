<?php
declare(strict_types=1);

namespace App\HttpClient\Tmdb;

use App\Enum\MediaTypeEnum;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final readonly class TmdbApiClient implements TmdbApiClientInterface
{
    private const string BASE_URL = 'https://api.themoviedb.org/3';
    public const string PUBLIC_IMAGE_BASE_URL = 'https://image.tmdb.org/t/p/original';

    public function __construct(
        private HttpClientInterface $httpClient,
        #[Autowire(env: 'TMDB_API_TOKEN')]
        private string $apiToken
    ) {
    }

    public function getTrendingTvShows(
        string $timeWindow = 'week',
        string $language = 'en-US',
        int $page = 1
    ): array {
        $queryParams = [
            'language' => $language,
            'page' => $page
        ];

        $response = $this->httpClient->request('GET', sprintf("%s/trending/tv/%s", self::BASE_URL, $timeWindow), [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->apiToken,
                'Accept' => 'application/json',
            ],
            'query' => $queryParams
        ]);

        return $response->toArray();
    }

    public function getTrendingMovies(
        string $timeWindow = 'week',
        string $language = 'en-US',
        int $page = 1
    ): array {
        $queryParams = [
            'language' => $language,
            'page' => $page
        ];

        $response = $this->httpClient->request('GET', sprintf("%s/trending/movie/%s", self::BASE_URL, $timeWindow), [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->apiToken,
                'Accept' => 'application/json',
            ],
            'query' => $queryParams
        ]);

        return $response->toArray();
    }

    public function getTvShowDetails(int $tvShowId, ?string $language = null): array
    {
        $options = [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->apiToken,
                'Accept' => 'application/json',
            ],
        ];

        if ($language !== null) {
            $options['query'] = ['language' => $language];
        }

        $response = $this->httpClient->request(
            'GET',
            self::BASE_URL . '/tv/' . $tvShowId,
            $options
        );

        return $response->toArray();
    }

    public function getMovieDetails(int $id, ?string $language = null): array
    {
        $options = [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->apiToken,
                'Accept' => 'application/json',
            ],
        ];

        if ($language !== null) {
            $options['query'] = ['language' => $language];
        }

        $response = $this->httpClient->request(
            'GET',
            self::BASE_URL . '/movie/' . $id,
            $options
        );

        return $response->toArray();
    }

    public function getTvShowTranslations(int $id): array
    {
        $response = $this->httpClient->request('GET', sprintf("%s/tv/%s/translations", self::BASE_URL, $id), [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->apiToken,
                'Accept' => 'application/json',
            ],
        ]);

        return $response->toArray();
    }

    public function getMovieTranslations(int $id): array
    {
        $response = $this->httpClient->request('GET', sprintf("%s/movie/%s/translations", self::BASE_URL, $id), [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->apiToken,
                'Accept' => 'application/json',
            ],
        ]);

        return $response->toArray();
    }

    public function searchTvShows(
        string $query,
        ?int $firstAirDateYear = null,
        bool $includeAdult = false,
        string $language = 'en-US',
        int $page = 1
    ): array {
        $queryParams = [
            'query' => $query,
            'include_adult' => $includeAdult,
            'language' => $language,
            'page' => $page
        ];

        if ($firstAirDateYear !== null) {
            $queryParams['first_air_date_year'] = (string)$firstAirDateYear;
        }

        $response = $this->httpClient->request('GET', sprintf("%s/search/tv", self::BASE_URL), [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->apiToken,
                'Accept' => 'application/json',
            ],
            'query' => $queryParams
        ]);

        return $response->toArray();
    }

    public function searchMovies(string $query, ?string $primaryReleaseYear = null, bool $includeAdult = false, string $language = 'en-US', int $page = 1): array
    {
        $queryParams = [
            'query' => $query,
            'include_adult' => $includeAdult,
            'language' => $language,
            'page' => $page
        ];

        if ($primaryReleaseYear !== null) {
            $queryParams['first_air_date_year'] = $primaryReleaseYear;
        }

        $response = $this->httpClient->request('GET', sprintf("%s/search/movie", self::BASE_URL), [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->apiToken,
                'Accept' => 'application/json',
            ],
            'query' => $queryParams
        ]);

        return $response->toArray();
    }

    public function getVideos(MediaTypeEnum $mediaType, int $id, string $language = 'en-US'): array
    {
        $response = $this->httpClient->request('GET', sprintf("%s/%s/%s/videos", self::BASE_URL, $mediaType->value, $id), [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->apiToken,
                'Accept' => 'application/json',
            ],
            'query' => compact('language')
        ]);

        return $response->toArray();
    }

    public function getProviders(MediaTypeEnum $mediaType, int $id): array
    {
        $response = $this->httpClient->request('GET', sprintf("%s/%s/%s/watch/providers", self::BASE_URL, $mediaType->value, $id), [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->apiToken,
                'Accept' => 'application/json',
            ]
        ]);

        return $response->toArray();
    }
}
