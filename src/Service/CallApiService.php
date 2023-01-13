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
        $pokemon['description'] = $pokemonSpecies['flavor_text_entries'][0]['flavor_text'];

        //on change le poids et la taille pour un affichage en kilogrammes et en mètres
        $pokemon['weight'] = floatval($pokemon['weight'])/10;
        $pokemon['height'] = floatval($pokemon['height'])/10;
        $pokemon['shape'] = $pokemonSpecies['shape']['name'];

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

        $apiCallsPokemon = curl_multi_init();
        $curl_arr = array();
        $speciesCount = count($arrayDataGen['pokemon_species']);
        //foreach ($arrayDataGen['pokemon_species'] as $pokemonSpecy)
        for($i = 0; $i < $speciesCount; $i++)
        {
            //On isole le numéro national car l'API renvoit parfois des erreurs avec le nom du pokémon
            $arrayExplodeUrl =  explode('https://pokeapi.co/api/v2/pokemon-species/', $arrayDataGen['pokemon_species'][$i]['url']);
            $url = 'https://pokeapi.co/api/v2/pokemon/' . $arrayExplodeUrl[1];
            $curl_arr[$i] = curl_init($url);
            curl_setopt($curl_arr[$i], CURLOPT_RETURNTRANSFER, true);
            curl_multi_add_handle($apiCallsPokemon, $curl_arr[$i]);
//            $nationalIdPokemon = $arrayExplodeUrl[1];
//
//            $response = $this->client->request(
//                'GET',
//                'https://pokeapi.co/api/v2/pokemon/' . $nationalIdPokemon
//            );
//            $arrayPokemon = $response->toArray();
//            array_push($arrayDataPokemon, $arrayPokemon);
        }

        do {
            curl_multi_exec($apiCallsPokemon,$running);
            curl_multi_select($apiCallsPokemon);
            print_r($running . " ");
        } while($running > 0);

        print_r("\n");

        for($j = 0; $j < $speciesCount; $j++)
        {
            $data = curl_multi_getcontent($curl_arr[$j]);
            print_r("2023");
            print_r($data);
            $json_data = json_encode($data);
            //dd($json_data);
            array_push($arrayDataPokemon, $json_data);
//            $results = curl_multi_getcontent  ( $curl_arr[$i]  );
//            echo( $i . "\n" . $results . "\n");
        }
        //curl_multi_close($apiCallsPokemon);
        dd($arrayDataPokemon);
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