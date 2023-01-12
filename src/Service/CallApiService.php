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
//        dd($arrayDataPokemon);
        return $arrayDataPokemon;
    }

//    //fonction pour trier la liste de pokemon par numéro national
//    public function sortGenerationPokemonById($generationPokemon): array
//    {
//        $generationPokemonTri = new ArrayObject($generationPokemon);
//
//        return usort($generationPokemon, $generationPokemon['id']);
//    }

    function sortGenerationPokemon($arrayPokemon, $field, $order=SORT_ASC)
    {
        $new_array = array();
        $sortable_array = array();

        if (count($arrayPokemon) > 0) {
            foreach ($arrayPokemon as $k => $v) {
                if (is_array($v)) {
                    foreach ($v as $k2 => $v2) {
                        if ($k2 == $field) {
                            $sortable_array[$k] = $v2;
                        }
                    }
                } else {
                    $sortable_array[$k] = $v;
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