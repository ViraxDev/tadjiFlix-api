<?php

declare(strict_types=1);

namespace App\State\Provider\Movie;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\DTO\MovieResult;
use App\DTO\MovieSearchResults;
use App\HttpClient\Tmdb\TmdbApiClient;
use App\HttpClient\Tmdb\TmdbApiClientInterface;
use Symfony\Component\HttpFoundation\RequestStack;

final readonly class MovieSearchProvider implements ProviderInterface
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

        $searchResults = $this->tmdbApiClient->searchMovies(
            $query,
            $request->query->get('primary_release_year'),
            $request->query->getBoolean('include_adult'),
            $request->query->get('language', 'en-US'),
            $request->query->getInt('page', 1)
        );

        $results = new MovieSearchResults();
        $results->page = $searchResults['page'] ?? 1;
        $results->total_pages = $searchResults['total_pages'] ?? 0;
        $results->total_results = $searchResults['total_results'] ?? 0;

        if (isset($searchResults['results']) && is_array($searchResults['results'])) {
            foreach ($searchResults['results'] as $item) {
                $movie = new MovieResult();
                foreach ($item as $property => $value) {
                    if (property_exists($movie, $property)) {
                        if (($property === 'poster_path' || $property === 'backdrop_path') && !empty($value)) {
                            $value = sprintf('%s%s', TmdbApiClient::PUBLIC_IMAGE_BASE_URL, $value);
                        }

                        $movie->$property = $value;
                    }
                }
                $results->results[] = $movie;
            }
        }

        return $results;
    }
}
