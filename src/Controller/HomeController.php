<?php

namespace App\Controller;

use App\Service\CallApiService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\VarDumper\Cloner\VarCloner;
use Symfony\Component\VarDumper\Dumper\HtmlDumper;
use Symfony\Component\VarDumper\Dumper\CliDumper;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="app_home")
     */
    public function index(CallApiService $callApiService): Response
    {
        //test du fonctionnement de la récupération de données sur l'API
        $pokemons = $callApiService->getGenerationPokemonData();
        //Faire en sorte que le tri puisse être choisi avec un bouton
        $pokemonsSorted = $callApiService->sortGenerationPokemon($pokemons, 'types');

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'pokemonsSorted' => $pokemonsSorted
        ]);
    }
}
