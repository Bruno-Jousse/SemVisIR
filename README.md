# SemVisIR

Application de recherche d'image.

## Introduction

L'application a été réalisée en PHP au format MVC, l'affichage se fait en HTML/CSS avec du Javascript. 

L'index.php est dynamique et affiche la page correspondant au module demandé. Il conserve le même template (que l'on retrouve avec le CSS dans le dossier structurePage) et récupère dans la Vue le titre et le contenu à afficher au niveau de la section.

Chaque Module est présent dans un dossier différent (accueil, recherche) et possède un fichier Module(qui contient le Contrôleur et les exceptions), un fichier Contrôleur(qui contient la Vue et le Modèle), un fichier Modèle et un fichier Vue.

Les modules, contrôleurs, modèles et vues étendent respectivement moduleGénérique, controlleurGénérique, modèleGénérique et vueGénérique se touvant dans le dossier pagesGénériques. Ces classes possèdent les fonctions et variables utilisées par tous les modules (ex: connexion dans modèle, contenu à afficher dans la vue).

Le Contrôleur analyse les valeurs qu'il reçoit et il appelle les fonctions adaptées présentes dans le modèle et la vue.

Les fonctions du Modèle exécutent des algorithmes et des calculs et retournent au contrôleur les résultats obtenus.

Les fonctions de la Vue permette de définir ce qui sera affiché par l'index, à l'intérieur de la variable $contenu.


## Les modules

### Module Accueil

Accueil est la page initiale ou en cliquant sur la bannière, elle contient le formulaire dans lequel il faut sélectionner une image, un mode d'affichage et un algorithme


### Module Recherche

Recherche est le module le plus conséquent de l'application, le contrôleur récupère les informations envoyées, exécute le bon algorithme qui va écrire dans les fichiers du dossier category les images correspondantes, leurs catégories et similarité. Puis il demande au modèle d'obtenir les métadonnées de l'image ainsi que les images ainsi retournées par l'algorithme. Enfin il appelle la vue ne lui envoyant les informations obtenues, qui affichera ces informations selon l'affichage choisi.

## Les autres dossiers

### Images

Le dossier images possède l'ensemble des images sélectionnables par l'algorithme, ainsi que les annotations des images qui permettent d'obtenir les informations basiques sur les images (lieu, date...).

### tierApp

Le dossier tierApp regroupe tous les scripts tiers, les frameworks qui sont utilisés dans le site (ex: Bootstrap, Cytoscape, Lightbox2).
