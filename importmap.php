<?php

/**
 * Definit les modules JavaScript exposes via l'importmap de l'application.
 *
 * - "path" designe le chemin resolu par l'Asset Mapper Symfony.
 * - "entrypoint" indique les modules injectes directement dans les pages Twig.
 *
 * La commande "importmap:require" permet d'ajouter de nouvelles dependances ici.
 */
return [
    'app' => [
        'path' => './assets/app.js',
        'entrypoint' => true,
    ],
];
