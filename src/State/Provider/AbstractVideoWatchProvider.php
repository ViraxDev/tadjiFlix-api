<?php

declare(strict_types=1);

namespace App\State\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\DTO\VideoResults;
use App\DTO\VideoResult;
use App\Enum\MediaTypeEnum;
use App\HttpClient\Tmdb\TmdbApiClient;
use App\HttpClient\Tmdb\TmdbApiClientInterface;
use Symfony\Component\HttpFoundation\RequestStack;

abstract class AbstractVideoWatchProvider implements ProviderInterface
{
    public function __construct(
        protected TmdbApiClientInterface $tmdbApiClient,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $results = new VideoResults();
        $results->results = $this->tmdbApiClient->getProviders(
            $this->getMediaType(),
            (int) $uriVariables['id']
        )['results'] ?? [];

        foreach ($results->results as $locale => $result) {
            if (isset($results->results[$locale]['flatrate'][0]) && array_key_exists('logo_path', $results->results[$locale]['flatrate'][0])) {
                $results->results[$locale]['flatrate'][0]['logo_path'] = TmdbApiClient::PUBLIC_IMAGE_BASE_URL . $results->results[$locale]['flatrate'][0]['logo_path'];
            }
        }

        return $results;
    }

    abstract protected function getMediaType(): MediaTypeEnum;
}
