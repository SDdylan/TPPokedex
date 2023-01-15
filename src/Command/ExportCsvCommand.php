<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ExportCsvCommand extends Command
{
    protected static $defaultName = 'app:export-csv';
    protected static $defaultDescription = 'Command to export data of a pokemon to CSV file';

    private $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
        parent::__construct();

    }
    
    protected function configure()
    {
        $this
            ->addArgument('generation', InputArgument::OPTIONAL, 'Generation of pokémon to export to CSV (int)')
            ->setHelp('This command allows you to export data from all the pokemon of a given generation')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $arg1 = $input->getArgument('generation');

        //On verifie que l'utilisateur à choisi la bonne génération
        if ($arg1 <= 9 && $arg1 >= 1) {
            $io->note(sprintf('You passed an argument: %s', $arg1));

            //recupération de la génération
            $response = $this->client->request(
                'GET',
                'https://pokeapi.co/api/v2/generation/' . $arg1
            );
            $arrayDataGen = $response->toArray();

            // Ouverture/Création d'un fichier et exportation des données
            $out = fopen('pokemon.csv', 'w');
            fputcsv($out, array('name', 'id' ,'url'));
            foreach ($arrayDataGen['pokemon_species'] as $pokemonSpecy)
            {
                $id = explode('https://pokeapi.co/api/v2/pokemon-species/', $pokemonSpecy['url']);
                fputcsv($out, array($pokemonSpecy['name'], $id[1],  $pokemonSpecy['url']));
            }
            fclose($out);

            $io->success('Pokemon exported to pokemon.csv in root directory !');

            $return = Command::SUCCESS;
        } else {
            $io->error('argument must be a valid generation (between 1 & 9)');
            $return = Command::FAILURE;
        }
        
        return $return;
    }
}
