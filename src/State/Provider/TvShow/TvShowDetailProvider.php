<?php

declare(strict_types=1);

namespace App\State\Provider\TvShow;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\DTO\MediaProvider;
use App\DTO\TvShow;
use App\Enum\MediaTypeEnum;
use App\HttpClient\Tmdb\TmdbApiClient;
use App\HttpClient\Tmdb\TmdbApiClientInterface;
use Symfony\Component\HttpFoundation\RequestStack;

final readonly class TvShowDetailProvider implements ProviderInterface
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

        $data = $this->tmdbApiClient->getTvShowDetails((int) $id, $language);
        $providers = $this->tmdbApiClient->getProviders(MediaTypeEnum::TV_SHOW, (int) $id)['results'] ?? [];

        $this->hydrateTvShowInitialData($tvShow = new TvShow(), $data);
        $this->hydrateTvShowProvidersData($tvShow, $providers);

        return $tvShow;
    }

    private function hydrateTvShowInitialData(TvShow $tvShow, array $data): void
    {
        foreach ($data as $property => $value) {
            if (property_exists($tvShow, $property)) {
                if ($property === 'poster_path' && !empty($value)) {
                    $value = sprintf('%s%s', TmdbApiClient::PUBLIC_IMAGE_BASE_URL, $value);
                }
                if ($property === 'backdrop_path' && !empty($value)) {
                    $value = sprintf('%s%s', TmdbApiClient::PUBLIC_IMAGE_BASE_URL, $value);
                }

                $tvShow->$property = $value;
            }
        }

        if (isset($data['id'])) {
            $tvShow->translations = $this->tmdbApiClient->getTvShowTranslations($data['id'])['translations'] ?? null;
        }
    }

    private function hydrateTvShowProvidersData(TvShow $tvShow, array $providers): void
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

            $tvShow->providers[] = $dto;
        }
    }
}
