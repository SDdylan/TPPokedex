<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class CallApiService
{
    private $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    public function getGenerationPokemonData()
    {
        $response = $this->client->request(
            'GET',
            'https://pokeapi.co/api/v2/generation/1'
        );

        $arrayData = $response->toArray();
        return $arrayData['pokemon_species'];
    }
}