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

    //Récupération d'un pokémon en fonction de son ID
    public function getPokemonData($id): array
    {
        $response = $this->client->request(
            'GET',
            'https://pokeapi.co/api/v2/pokemon/' . $id
        );

        $pokemon = $response->toArray();

        //Ajout de le description du pokémon
        $response = $this->client->request(
            'GET',
            'https://pokeapi.co/api/v2/pokemon-species/' . $id
        );

        $pokemonSpecies = $response->toArray();
        //$description = $pokemonSpecies['flavor_text_entries'][0]['flavor_text'];

        //On ajoute la description du pokemon à l'array $pokemon
        //array_push($pokemon, $pokemonSpecies['flavor_text_entries'][0]['flavor_text']);
        $pokemon['description'] = $pokemonSpecies['flavor_text_entries'][0]['flavor_text'];
        dump($pokemon);
        //dd($pokemon);
        return $pokemon;

    }

    //Récupération de tout les pokémons d'une génération
    public function getGenerationPokemonData(): array
    {
        $response = $this->client->request(
            'GET',
            'https://pokeapi.co/api/v2/generation/' . $_ENV['POKEMON_GENERATION']
        );

        $arrayDataGen = $response->toArray();

        //création d'un array de array pour stocker l'image et le type en plus de l'url et le nom
        $arrayDataPokemon = array();

        foreach ($arrayDataGen['pokemon_species'] as $pokemon_specy)
        {
            $response = $this->client->request(
                'GET',
                'https://pokeapi.co/api/v2/pokemon/' . $pokemon_specy['name']
            );
            $arrayPokemon = $response->toArray();
            array_push($arrayDataPokemon, $arrayPokemon);
        }
        return $arrayDataPokemon;
    }

    //fonction pour trier la liste de pokemon par un attribut en particulier
    function sortGenerationPokemon($arrayPokemon, $field, $order=SORT_ASC)
    {
        $new_array = array();
        $sortable_array = array();

        if (count($arrayPokemon) > 0) {
            foreach ($arrayPokemon as $keyPokemon => $pokemon) {
                if (is_array($pokemon)) {
                    foreach ($pokemon as $pokemonFieldName => $pokemonFieldValue) {
                        if ($pokemonFieldName == $field) {
                            if(is_array($pokemonFieldValue)) {
                                $sortable_array[$keyPokemon] = $pokemonFieldValue[0]['type']['name'];
                            } else {
                                $sortable_array[$keyPokemon] = $pokemonFieldValue;
                            }
                        }
                    }
                } else {
                    $sortable_array[$keyPokemon] = $pokemon;
                }
            }

            switch ($order) {
                case SORT_ASC:
                    asort($sortable_array);
                    break;
                case SORT_DESC:
                    arsort($sortable_array);
                    break;
            }

            foreach ($sortable_array as $k => $v) {
                $new_array[$k] = $arrayPokemon[$k];
            }
        }

        return $new_array;
    }
}