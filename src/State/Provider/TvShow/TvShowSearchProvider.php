<?php

declare(strict_types=1);

namespace App\State\Provider\TvShow;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\DTO\TvShowResult;
use App\DTO\TvShowSearchResults;
use App\HttpClient\Tmdb\TmdbApiClient;
use App\HttpClient\Tmdb\TmdbApiClientInterface;
use Symfony\Component\HttpFoundation\RequestStack;

final readonly class TvShowSearchProvider implements ProviderInterface
{
    public function __construct(
        private TmdbApiClientInterface $tmdbApiClient,
        private RequestStack $requestStack
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $request = $this->requestStack->getCurrentRequest();
        if (!$request) {
            throw new \RuntimeException('Request not available');
        }

        $query = $request->query->get('query');
        if (!$query) {
            throw new \InvalidArgumentException('Query parameter is required');
        }

        $firstAirDateYear = $request->query->getInt('first_air_date_year');
        $includeAdult = $request->query->getBoolean('include_adult');
        $language = $request->query->get('language', 'en-US');
        $page = $request->query->getInt('page', 1);

        $searchResults = $this->tmdbApiClient->searchTvShows(
            $query,
            $firstAirDateYear !== 0 ? $firstAirDateYear : null,
            $includeAdult,
            $language,
            $page
        );

        $results = new TvShowSearchResults();
        $results->page = $searchResults['page'] ?? 1;
        $results->total_pages = $searchResults['total_pages'] ?? 0;
        $results->total_results = $searchResults['total_results'] ?? 0;

        if (isset($searchResults['results']) && is_array($searchResults['results'])) {
            foreach ($searchResults['results'] as $item) {
                $tvShow = new TvShowResult();
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
