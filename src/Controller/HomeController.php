<?php

namespace App\Controller;

use App\Service\CallApiService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="app_home")
     */
    public function index(CallApiService $callApiService): Response
    {
        // Récupération de tout les pokémons d'une génération
        $pokemons = $callApiService->getGenerationPokemonData();

        // On regarde par quels paramètres on trie les pokémons
        $tri = 'id';
        if (isset($_GET['tri'])) {
            if ($_GET['tri'] == 'types') {
                $tri = $_GET['tri'];
            } elseif ($_GET['tri'] == 'name') {
                $tri = $_GET['tri'];
            } else {
                $tri = 'id';
            }
        }
        // Faire en sorte que le tri puisse être choisi avec un bouton
        $pokemonsSorted = $callApiService->sortGenerationPokemon($pokemons, $tri);

        return $this->render('home/index.html.twig', [
            'generation' => $_ENV['POKEMON_GENERATION'],
            'pokemonsSorted' => $pokemonsSorted
        ]);
    }

    /**
     * @Route("/pokemon/{idPokemon}", name="app_pokemon_detail")
     */
    public function showPokemon(CallApiService $callApiService, int $idPokemon): Response
    {
        // Récupération des données du pokémon via un id
        $pokemon = $callApiService->getPokemonData($idPokemon);

        return $this->render('home/detail.html.twig', [
            'generation' => $_ENV['POKEMON_GENERATION'],
            'pokemon' => $pokemon
        ]);
    }
}
