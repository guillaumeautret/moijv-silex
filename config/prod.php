<?php

// configure your app for the production environment

$app['twig.path'] = array(__DIR__ . '/../templates');
$app['twig.options'] = array('cache' => __DIR__ . '/../var/cache/twig');

require_once __DIR__ . '/db.php';
// DIR veut dire aussi qu'on ait dans le dossier config = répertoire courant = répertoire ou se trouve notre fichier
