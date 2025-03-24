<?php

declare(strict_types=1);

namespace App\State\Provider\TvShow;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\DTO\TvShow;
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

        $tvShow = new TvShow();
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

        return $tvShow;
    }
}
