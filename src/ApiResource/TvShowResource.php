<?php

declare(strict_types=1);

namespace App\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Parameters;
use ApiPlatform\Metadata\QueryParameter;
use ApiPlatform\OpenApi\Model\Operation;
use App\DTO\TvShow;
use App\DTO\TvShowSearchResults;
use App\State\Provider\TvShowDetailProvider;
use App\State\Provider\TvShowSearchProvider;
use App\State\Provider\TvShowTrendingProvider;

#[ApiResource(
    shortName: 'TvShow',
    operations: [
        new Get(
            uriTemplate: '/tv/trending',
            openapi: new Operation(
                summary: 'Get trending TV shows',
                description: 'Get the weekly trending TV shows'
            ),
            output: TvShowSearchResults::class,
            name: 'trending_tv_shows',
            provider: TvShowTrendingProvider::class,
            parameters: new Parameters(
                [
                    new QueryParameter(
                        'page', schema: ['type' => 'int'], description: 'The page number', required: false
                    ),
                ]
            )
        ),
        new Get(
            uriTemplate: '/tv/search',
            openapi: new Operation(
                summary: 'Search for TV shows',
                description: 'Search for TV shows with various filtering options and pagination'
            ),
            output: TvShowSearchResults::class,
            name: 'search_tv_shows',
            provider: TvShowSearchProvider::class,
            parameters: new Parameters(
                [
                    new QueryParameter(
                        'query', schema: ['type' => 'string'], description: 'The search query', required: true
                    ),
                    new QueryParameter(
                        'include_adult', schema: ['type' => 'bool'], description: 'Include adult content', required: false
                    ),
                    new QueryParameter(
                        'language', schema: ['type' => 'string'], description: 'The language of the TV show', required: false
                    ),
                    new QueryParameter(
                        'page', schema: ['type' => 'int'], description: 'The page number', required: false
                    ),
                    new QueryParameter(
                        'first_air_date_year', schema: ['type' => 'int'], description: 'The year of the first air date', required: false
                    ),
                ]
            )
        ),
        new Get(
            uriTemplate: '/tv/{id}',
            openapi: new Operation(
                summary: 'Get details of a specific TV show',
                description: 'Retrieves the details of a TV show by ID with optional language parameter'
            ),
            output: TvShow::class,
            name: 'get_tv_show_detail',
            provider: TvShowDetailProvider::class
        ),
    ]
)]
final class TvShowResource
{
}
