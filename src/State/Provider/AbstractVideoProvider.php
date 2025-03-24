<?php

declare(strict_types=1);

namespace App\State\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\DTO\VideoResults;
use App\DTO\VideoResult;
use App\Enum\MediaTypeEnum;
use App\HttpClient\Tmdb\TmdbApiClientInterface;
use Symfony\Component\HttpFoundation\RequestStack;

abstract class AbstractVideoProvider implements ProviderInterface
{
    public function __construct(
        protected TmdbApiClientInterface $tmdbApiClient,
        protected RequestStack $requestStack,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $request = $this->requestStack->getCurrentRequest();
        $language = $request?->query->get('language', 'en-US');

        $videos = $this->tmdbApiClient->getVideos($this->getMediaType(), (int) $uriVariables['id'], $language);

        $results = new VideoResults();

        foreach ($videos['results'] ?? [] as $item) {
            $tvShow = new VideoResult();
            foreach ($item as $property => $value) {
                if (property_exists($tvShow, $property)) {
                    $tvShow->$property = $value;
                }
            }
            $results->results[] = $tvShow;
        }

        return $results;
    }

    abstract protected function getMediaType(): MediaTypeEnum;
}
