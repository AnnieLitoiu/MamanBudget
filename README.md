MamanSolo – Documentation du projet
Présentation

MamanSolo est une mini-application web réalisée en Symfony.
Le projet met en scène une maman solo qui doit gérer un foyer sur un mois virtuel.
L’objectif pédagogique est de sensibiliser aux difficultés financières et émotionnelles des familles monoparentales, tout en proposant une expérience ludique et interactive.

L’utilisateur incarne une maman et doit répartir son budget, gérer des imprévus, maintenir son bien-être et celui de ses enfants, et trouver un équilibre jusqu’à la fin du mois.

Ce projet est réalisé en collaboration par Annie, Orsula , Faouzia, Gaelle et Karima.



Fonctionnement

1. Accueil et introduction au jeu

L’utilisateur accède d’abord à une page d’accueil qui présente brièvement le concept du jeu et le but de l’expérience.
Cette introduction explique que l’utilisateur devra prendre des décisions chaque semaine et que ces choix auront un impact sur trois indicateurs :Budget, bien-être et bonheur des enfants.



2. Création du profil utilisateur

Avant de commencer, l’utilisateur crée son profil via plusieurs formulaires successifs :

Informations personnelles : nom, email, mot de passe

Situation familiale : type de foyer, nombre d’enfants, revenu mensuel

Apparence de l’avatar (gestion via un formulaire dédié)

Chaque formulaire enregistre les données dans la base grâce aux entités prévues dans Symfony (Utilisateur, Situation, Avatar).




3. Connexion

Une fois le compte créé, l’utilisateur peut se connecter et accéder à son espace de jeu personnel.
La gestion de la connexion est assurée par le système de sécurité intégré de Symfony.




4. Tableau de bord

Après connexion, l’utilisateur arrive sur son tableau de bord.
Il peut y consulter :

Son budget de la semaine

Les indicateurs du foyer

Éventuellement des informations sur sa situation

C’est depuis cette interface que le jeu démarre.




5. Déroulement d’une semaine de jeu

Chaque semaine, l’utilisateur est confronté à un événement.
Cet événement propose plusieurs choix possibles.
Chaque choix modifie une ou plusieurs valeurs :

Budget restant

Bien-être de la maman

Bonheur des enfants

Les décisions sont appliquées via la logique métier du projet.




6. Système de progression

Le jeu avance automatiquement d’une semaine à l’autre.
Au total, un mois virtuel est composé de quatre semaines.
À la fin de chaque semaine, un bilan peut être présenté.




7. Fin du mois et résumé

À la quatrième semaine, une synthèse finale est affichée avec :

Le budget final

Le niveau de bien-être

Le bonheur des enfants

Une appréciation générale

Cela permet à l’utilisateur de visualiser l’impact de ses décisions.

Architecture et approche technique
Symfony

Le projet utilise Symfony pour :

La gestion des entités (Utilisateur, Situation, Avatar, Partie, Semaine, Événements, Options)

Les contrôleurs responsables de l’affichage des pages

Le moteur de templates Twig

Le système de formulaires

Le système de sécurité (authentification)

JavaScript

Le JavaScript est utilisé dans les pages Twig pour dynamiser l’interface (mise à jour du DOM, interactions utilisateur).
Le fonctionnement du jeu ne repose pas sur une architecture API ; les contrôleurs Symfony renvoient principalement des vues Twig plutôt que du JSON.
Les décisions sont transmises via des formulaires ou appels AJAX directement à des routes Symfony classiques.

Logique métier

La gestion de la progression (semaine, choix, impacts) repose sur un service interne dédié.
Ce service est appelé depuis les contrôleurs du jeu pour appliquer les effets et mettre à jour l’état de la partie.

Chaque membre travaille sur sa propre branche puis effectue un merge vers la branche principale après validation.

Conclusion

MamanSolo propose une expérience à la fois simple d’accès et significative.
Le projet combine un design interactif en JavaScript, une structure solide en Symfony et un ensemble d’événements narratifs construits autour de la réalité des familles monoparentales.
Le travail collaboratif a permis d’intégrer plusieurs compétences complémentaires pour construire un jeu éducatif cohérent.
