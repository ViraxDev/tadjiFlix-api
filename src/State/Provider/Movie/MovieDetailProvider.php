<?php

declare(strict_types=1);

namespace App\State\Provider\Movie;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\DTO\MediaProvider;
use App\DTO\Movie;
use App\Enum\MediaTypeEnum;
use App\HttpClient\Tmdb\TmdbApiClient;
use App\HttpClient\Tmdb\TmdbApiClientInterface;
use Symfony\Component\HttpFoundation\RequestStack;

final readonly class MovieDetailProvider implements ProviderInterface
{
    public function __construct(
        private TmdbApiClientInterface $tmdbApiClient,
        private RequestStack $requestStack
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $id = $uriVariables['id'] ?? null;
        if (!$id) {
            throw new \InvalidArgumentException('TV show ID is required');
        }

        $language = $this->requestStack->getCurrentRequest()?->query->get('language');

        $data = $this->tmdbApiClient->getMovieDetails((int) $id, $language);
        $providers = $this->tmdbApiClient->getProviders(MediaTypeEnum::MOVIE, (int) $id)['results'] ?? [];

        $this->hydrateMovieInitialData($movie = new Movie(), $data);
        $this->hydrateTvShowProvidersData($movie, $providers);

        return $movie;
    }

    private function hydrateMovieInitialData(Movie $movie, array $data): void
    {
        foreach ($data as $property => $value) {
            if (property_exists($movie, $property)) {
                if ($property === 'poster_path' && !empty($value)) {
                    $value = sprintf('%s%s', TmdbApiClient::PUBLIC_IMAGE_BASE_URL, $value);
                }
                if ($property === 'backdrop_path' && !empty($value)) {
                    $value = sprintf('%s%s', TmdbApiClient::PUBLIC_IMAGE_BASE_URL, $value);
                }

                $movie->$property = $value;
            }
        }

        if (isset($data['id'])) {
            $movie->translations = $this->tmdbApiClient->getMovieTranslations($data['id'])['translations'] ?? null;
        }
    }

    private function hydrateTvShowProvidersData(Movie $movie, array $providers): void
    {
        foreach ($providers as $locale => $providerData) {
            $dto = new MediaProvider();
            $dto->locale = $locale;

            $dto->link = $providerData['link'] ?? '';
            $dto->logo_path = $providerData['flatrate'][0]['logo_path'] ?? '';
            $dto->logo_path = sprintf('%s%s', TmdbApiClient::PUBLIC_IMAGE_BASE_URL, $dto->logo_path);
            $dto->provider_id = $providerData['flatrate'][0]['provider_id'] ?? 0;
            $dto->provider_name = $providerData['flatrate'][0]['provider_name'] ?? '';
            $dto->display_priority = $providerData['flatrate'][0]['display_priority'] ?? 0;

            $movie->providers[] = $dto;
        }
    }
}
