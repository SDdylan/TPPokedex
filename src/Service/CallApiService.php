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

        $arrayDataGen = $response->toArray();

        //création d'un array de array pour stocker l'image et le type en plus de l'url et le nom
        $arrayDataPokemon = array(array());

        foreach ($arrayDataGen['pokemon_species'] as $dataGen)
        {
            $response = $this->client->request(
                'GET',
                'https://pokeapi.co/api/v2/pokemon/' . $dataGen['name']
            );
            $arrayPokemon = $response->toArray();
            array_push($arrayDataPokemon, array('name' => $dataGen['name'], 'url' => $dataGen['url'], 'sprite' => $arrayPokemon['sprites']['front_default'], 'type' => $arrayPokemon['types']));
        }

//        dd($arrayDataPokemon);
        return $arrayDataPokemon;
    }
}