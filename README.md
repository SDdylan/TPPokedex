# TP Pokedex

Ce projet est un pokédex recensant les pokémons d'une génération grâce à l'API [PokéAPI](https://pokeapi.co/).

## Fonctionnalités

Les différentes  pages et fonctionnalités de ce projet sont les suivantes :
* Page d'accueil regroupant les pokémons
* Page de détail d'un pokémon donné
* Commande pour exporter les données des pokémons dans un fichier CSV

## Technologies

* WampServer 3.2.6
    * Apache 2.4.51
    * PHP 7.4.29
* Bootstrap 5.1.3
* Composer 2.3.5 
* Symfony 5.4.8

## Installation

### Configuration de l'environnement

Il est nécessaire d'avoir un environnement local avec PHP et Apache.  
Pour la configuration d'un VirtualHost je vous laisse le soin de consulter la documentation de votre plateforme de développement web (par exemple: [WAMP](https://www.wampserver.com/) ou [XAMPP](https://doc.ubuntu-fr.org/xampp)).

### Déploiement du projet

Téléchargez manuellement le contenu de ce dépôt GitHub dans un dossier de votre système.
Vous pouvez également utiliser Git avec un terminal à la racine d'un dossier de votre choix :
```
git clone https://github.com/SDdylan/TPPokedex.git
```
Pour la prochaine étape, vous aurez besoin de [**Composer**](https://getcomposer.org/download/), veillez à l'installer si vous ne disposez pas déjà de ce dernier sur votre système.  
Installez ensuite les librairies de ce projet à l'aide d'un terminal à la racine de l'application avec la commande ci-dessous :
```
composer install
```
Pour générer le CSS, le projet passe par la librairie Webpack Encore, il vous faudra taper cette commande pour générer un build :
```````
npm run watch
```````
### Choix de la génération

Veillez dans un premier temps à changer la valeur de la génération dans le fichier **.env**, il s'agit de la variable *POKEMON_GENERATION*.

## Commande

La commande permettant de créer ou modifier un fichier CSV à la racine du projet contenant des données de tous les pokémons d'une génération est la suivante :
```````
//$int correspond à la génération dont vous souhaitez extraire les données

php bin/console app:export-csv $int
```````