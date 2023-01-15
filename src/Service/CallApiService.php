<?php

namespace App\Service;

class CallApiService
{

    //Récupération d'un pokémon en fonction de son ID
    public function getPokemonData($id): array
    {

        // On crée un array pour stocker les url à utiliser
        $urls = array('https://pokeapi.co/api/v2/pokemon-species/' . $id, 'https://pokeapi.co/api/v2/pokemon/' . $id);
        $urlsCount = count($urls);

        // On initialise un cURL multiple et on crée un array pour stocker ses résultats
        $curlArr = array();
        $master = curl_multi_init();

        for($i = 0; $i < $urlsCount; $i++)
        {
            // URL à partir de laquelle les données seront récupérées
            $curlArr[$i] = curl_init($urls[$i]);
            curl_setopt($curlArr[$i], CURLOPT_RETURNTRANSFER, true);
            curl_multi_add_handle($master, $curlArr[$i]);
        }

        do {
            curl_multi_exec($master,$running);
        } while($running > 0);

        // On stocke le résultat des requêtes dans des variables
        $pokemonSpecy = json_decode(curl_multi_getcontent  ($curlArr[0]), true);
        $pokemon = json_decode(curl_multi_getcontent  ($curlArr[1]), true);

        //On ajoute la description du pokemon à l'array $pokemon
        $pokemon['description'] = $pokemonSpecy['flavor_text_entries'][0]['flavor_text'];

        //on change le poids et la taille pour un affichage en kilogrammes et en mètres
        $pokemon['weight'] = floatval($pokemon['weight'])/10;
        $pokemon['height'] = floatval($pokemon['height'])/10;
        $pokemon['shape'] = $pokemonSpecy['shape']['name'];

        return $pokemon;
    }

    // Récupération de tout les pokémons d'une génération
    public function getGenerationPokemonData(): array
    {
        // Récupération des données de la génération de pokémon (pour l'id de chaque pokémon)
        $curl = curl_init();

        // configuration des options
        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://pokeapi.co/api/v2/generation/' . $_ENV['POKEMON_GENERATION'],
            CURLOPT_RETURNTRANSFER => 1
        ]);

        // Exécution de la requête
        $pokemonGen = json_decode(curl_exec($curl), true);
        // Fermeture des ressources
        curl_close($curl);

        // Récupération des données de tout les pokémon de la génération
        // Création d'un array pour stocker les URL de chaque pokémon
        $pokemonUrls = array();

        foreach($pokemonGen['pokemon_species'] as $k => $pokemonSpecy) {
            //On récupère l'id dans arrayExplodeUrl[1]
            $arrayExplodeUrl =  explode('https://pokeapi.co/api/v2/pokemon-species/', $pokemonSpecy['url']);
            array_push($pokemonUrls, 'https://pokeapi.co/api/v2/pokemon/' . $arrayExplodeUrl[1]);
        }

        // On initialise un cURL multiple et on crée un array pour stocker ses résultats
        $curlArr = array();
        $master = curl_multi_init();

        foreach($pokemonUrls as $k => $pokemonUrl)
        {
            $curlArr[$k] = curl_init($pokemonUrl);
            curl_setopt($curlArr[$k], CURLOPT_RETURNTRANSFER, true);
            curl_multi_add_handle($master, $curlArr[$k]);
        }

        do {
            curl_multi_exec($master,$running);
        } while($running > 0);

        // Array pour stocker les résultats
        $pokemonsData = array();
        foreach($pokemonUrls as $k => $pokemonUrl)
        {
            $pokemonsData[$k] = json_decode(curl_multi_getcontent  ($curlArr[$k]), true);
        }

        return $pokemonsData;
    }

    // Fonction pour trier la liste de pokemon par un attribut en particulier
    function sortGenerationPokemon($arrayPokemon, $field)
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

            foreach ($sortable_array as $k => $v) {
                $new_array[$k] = $arrayPokemon[$k];
            }
        }

        return $new_array;
    }

}