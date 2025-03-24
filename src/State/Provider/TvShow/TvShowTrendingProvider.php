<?php

declare(strict_types=1);

namespace App\State\Provider\TvShow;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\DTO\TvShow;
use App\DTO\TvShowSearchResults;
use App\HttpClient\Tmdb\TmdbApiClient;
use App\HttpClient\Tmdb\TmdbApiClientInterface;
use Symfony\Component\HttpFoundation\RequestStack;

final readonly class TvShowTrendingProvider implements ProviderInterface
{
    public function __construct(
        private TmdbApiClientInterface $tmdbApiClient,
        private RequestStack $requestStack,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $request = $this->requestStack->getCurrentRequest();
        $page = $request?->query->getInt('page', 1);

        $trendingResults = $this->tmdbApiClient->getTrendingTvShows(page: $page);

        $results = new TvShowSearchResults();
        $results->page = $trendingResults['page'] ?? 1;
        $results->total_pages = $trendingResults['total_pages'] ?? 0;
        $results->total_results = $trendingResults['total_results'] ?? 0;

        if (isset($trendingResults['results']) && is_array($trendingResults['results'])) {
            foreach ($trendingResults['results'] as $item) {
                $tvShow = new TvShow();
                foreach ($item as $property => $value) {
                    if (property_exists($tvShow, $property)) {
                        if (($property === 'poster_path' || $property === 'backdrop_path') && !empty($value)) {
                            $value = sprintf('%s%s', TmdbApiClient::PUBLIC_IMAGE_BASE_URL, $value);
                        }
                        $tvShow->$property = $value;
                    }
                }
                $results->results[] = $tvShow;
            }
        }

        return $results;
    }
}