<?php

declare(strict_types=1);

namespace App\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Parameters;
use ApiPlatform\Metadata\QueryParameter;
use ApiPlatform\OpenApi\Model\Operation;
use App\DTO\Movie;
use App\DTO\MovieSearchResults;
use App\DTO\ProviderResults;
use App\DTO\VideoResults;
use App\State\Provider\Movie\MovieDetailProvider;
use App\State\Provider\Movie\MovieSearchProvider;
use App\State\Provider\Movie\MovieTrendingProvider;
use App\State\Provider\Movie\MovieVideoProvider;
use App\State\Provider\Movie\MovieVideoWatchProvider;

#[ApiResource(
    shortName: 'Movie',
    operations: [
        new Get(
            uriTemplate: '/movie/trending',
            openapi: new Operation(
                summary: 'Get trending movies',
                description: 'Get the weekly trending movies'
            ),
            output: MovieSearchResults::class,
            name: 'trending_movies',
            provider: MovieTrendingProvider::class,
            parameters: new Parameters(
                [
                    new QueryParameter(
                        'page', schema: ['type' => 'int'], description: 'The page number', required: false
                    ),
                ]
            )
        ),
        new Get(
            uriTemplate: '/movie/search',
            openapi: new Operation(
                summary: 'Search for movies',
                description: 'Search for movies with various filtering options and pagination'
            ),
            output: MovieSearchResults::class,
            name: 'search_movies',
            provider: MovieSearchProvider::class,
            parameters: new Parameters(
                [
                    new QueryParameter(
                        'query', schema: ['type' => 'string'], description: 'The search query', required: true
                    ),
                    new QueryParameter(
                        'include_adult', schema: ['type' => 'bool'], description: 'Include adult content', required: false
                    ),
                    new QueryParameter(
                        'language', schema: ['type' => 'string'], description: 'The language of the movie', required: false
                    ),
                    new QueryParameter(
                        'page', schema: ['type' => 'int'], description: 'The page number', required: false
                    ),
                    new QueryParameter(
                        'primary_release_year', schema: ['type' => 'int'], description: 'The year of the primary release', required: false
                    ),
                ]
            )
        ),
        new Get(
            uriTemplate: '/movie/{id}/videos',
            openapi: new Operation(
                summary: 'Get videos of a specific movie',
                description: 'Retrieves the videos of a movie by ID'
            ),
            output: VideoResults::class,
            name: 'get_movie_videos',
            provider: MovieVideoProvider::class,
            parameters: new Parameters(
                [
                    new QueryParameter(
                        'language', schema: ['type' => 'string'], description: 'The language of the video', required: false
                    ),
                ]
            )
        ),
        new Get(
            uriTemplate: '/movie/{id}/watch/providers',
            openapi: new Operation(
                summary: 'Get video providers of a specific movie',
                description: 'Get the list of streaming providers we have for a movie.'
            ),
            output: ProviderResults::class,
            name: 'get_movie_providers',
            provider: MovieVideoWatchProvider::class
        ),
        new Get(
            uriTemplate: '/movie/{id}',
            openapi: new Operation(
                summary: 'Get details of a specific movie',
                description: 'Retrieves the details of a movie by ID with optional language parameter'
            ),
            output: Movie::class,
            name: 'get_movie_detail',
            provider: MovieDetailProvider::class
        ),
    ]
)]
final class MovieResource
{
}
