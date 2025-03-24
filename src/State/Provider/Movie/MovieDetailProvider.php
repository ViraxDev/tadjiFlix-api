<?php

declare(strict_types=1);

namespace App\State\Provider\Movie;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\DTO\Movie;
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

        $movie = new Movie();
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

        return $movie;
    }
}
