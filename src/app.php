<?php

use Silex\Application;
use Silex\Provider\AssetServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\ServiceControllerServiceProvider;
use Silex\Provider\HttpFragmentServiceProvider;
use Silex\Provider\FormServiceProvider;
use Silex\Provider\ValidatorServiceProvider;

// On utitilise le use quand on a plusieurs nom d'une même classe pour pas se répéter

$app = new \App\CustomApp();
$app->register(new ServiceControllerServiceProvider());
$app->register(new AssetServiceProvider());
$app->register(new TwigServiceProvider());
$app->register(new HttpFragmentServiceProvider());
$app['twig'] = $app->extend('twig', function ($twig, $app) {
    // add custom globals, filters, tags, ...

    return $twig;
});

$app['users.dao'] = function($app) {
    return new \DAO\UserDAO($app['pdo']);
};

$app['categories.dao'] = function($app) {
    return new \DAO\CategoryDAO($app['pdo']);
};

$app['loaning.dao'] = function($app) {
    return new \DAO\LoaningDAO($app['pdo']);
};

$app['games.dao'] = function($app) {
    return new \DAO\GameDAO($app['pdo']);
};


$app['pdo'] = function($app) {
    $options = $app['pdo.options'];
    return new \PDO("{$options['dbms']}://host={$options['host']};dbname={$options['dbname']};charset={$options['charset']}", $options['username'], $options['password'], array(
        \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
        \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION
    ));
};

$app->register(new Silex\Provider\SessionServiceProvider());

$app->register(new Silex\Provider\SecurityServiceProvider(), array(
    'security.firewalls' => [
        'admin' => array(
            'pattern' => '^/admin/', // le pattern : toutes les uri qui commencent par admin par ce firewall, toutes les url avec admin protégé. Protege notre backoffice
            'form' => array(
                'login_path' => '/loginadmin',
                'check_path' => '/admin/login_check',
                'always_use_default_target_path' => true,
                'default_target_path' => '/admin/dashboard'
                ),
            //'http' => true,
            'anonymous' => false,
            'logout' => array('logout_path' => '/admin/logoutadmin', 'invalidate_session' => true),
            'users' => function () use ($app) {
                return $app['admins.dao'];
            },
//            'form_login' =>array(
//                'default_target_path' => 'admin_dashboard', // fonctionne pas, voir ci-dessus
//            ),
        ),
        'front' => array(
            'pattern' => '^/', // pattern = motif : ressemble à une URI : correspond à toutes les routes qui correspond aux firewall. on ne met pas '/admin', 
            //on met juste '/' pour mettre firwaal sur tout le front office. c'est le firewall d'authentification
            'http' => true,
            'anonymous' => true, // pour autoriser les connexions anonymes
            'form' => array('login_path' => '/login', 'check_path' => '/login_check'), // formulaire de connexion, on a deux routes (/login, /login_check)
            'logout' => array('logout_path' => '/logout', 'invalidate_session' => true),
            'users' => function () use ($app) {
                return $app['users.dao']; // c'est çà qui va joué le role de UserProviders. users.dao : chargez les utilisateurs?
            }
        ),
        
    ]
));
// on créé un firewall : espace protégé : concerne toutes les uri qui comment par /. 
// La connexion se fera http par post via formaluaire. 
// Anonymous autorise les connexions annonyme (si absent utilisateur redirigé vers formulaire de connexion de login).
// Un fournisseur d'uilisateurs va être notre dao : remplit son job remplit cahier des charges UserProviders??
// Front = nom de notr firewall


$app->register(new Silex\Provider\LocaleServiceProvider());
$app->register(new Silex\Provider\TranslationServiceProvider(), array(
    'locale_fallbacks' => array('fr'),
    'translator.domains' => [
        'messages' => [
            'fr' => [
                'The credentials were changed from another session.' => 'Les identifiants ont été changés dans une autre session.',
                'The presented password cannot be empty.' => 'Le mot de passe ne peut pas être vide.',
                'The presented password is invalid.' => 'Le mot de passe entré est invalide.',
                'Bad credentials.' => 'Les identifiants sont incorrects'
            ]
        ]
    ]
));

$app->register(new FormServiceProvider());
$app->register(new ValidatorServiceProvider());

$app['admins.dao'] = function($app){
    return new DAO\AdminDAO($app['pdo']);
};

return $app;
