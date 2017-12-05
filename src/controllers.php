<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

//Request::setTrustedProxies(array('127.0.0.1'));

require __DIR__.'/controllers_admin.php';


$app->before(function() use ($app) {
    // une fonction est isolé, accède pas aux variables global. Fonction anonyme peut appelé variable global en utilisant le mot use. 
    // Equivaut à l'injection en argument de la fonction mais faut précisé le type (\symfony\....)
    // before : bout de code qui s'éxécutera avant n'importe qu'elle route, s'éxécute quoi qu'il arrive
    $token = $app['security.token_storage']->getToken();
    
    if($token){
        $user = $token->getUser();
    }else{
        $user = null;
    }
    //silex utilise un systeme de token
    // silex ne stock pas un utilisateur directement, il stock dans un token (contient plein de chose, toutes données de l'utilisateur sauf peut etre mdp),
    //A l'intérieur de celui-ci on peut récup un utilisateur
    
    if(is_string($user)){
        // si l'utilisateur est anonyme
        $user = null;
    }
    
    $app['user'] = $user;
    //$app disponible dans importe quel endroit du template twig
});



$app->get('/', function () use ($app) {

    
    return $app['twig']->render('index.html.twig');
})
->bind('homepage')
;

        
$app->get('/login', function(Request $request) use ($app) {
    return $app['twig']->render('login.html.twig', array(
        'error'         => $app['security.last_error']($request),
        'last_username' => $app['session']->get('_security.last_username'),
    ));
})->bind('login')
;
// parametre $request injecté par silex, objet créé par silex pour incarné la requete qui a été faites par l'utilisateur, évite de passé par $_POST['']

$app->match('/register', function(Request $request) use ($app){
    // match : n'importe qu'elle méthode (get ou post)
    // match : permet matcher plusieurs méthodes d'un coup, et peut filtrer cette méthode à la fin (ici get et post)
    // $request mis en argument dans la fonction pour pouvoir récup info formulaire???
    $user = new \Entity\User();
    //var_dump($user);
    
    $form = $app['form.factory']->createBuilder(\FormType\UserType::class, $user, [
        'validation_groups' => ['registration'] // par défaut c'est édition, on met registration dans le cas de la modif
    ])
        ->add('submit', SubmitType::class, [
            'label' => 'Envoyer'
        ])
        ->getForm();
    //parametre de create builder : -le type, -l'entité et -un tableau d'options
    
    $form->handleRequest($request);
    //handleRequest : traite, gere et prend en parametre objet $request
    //Etape de validation
    // Les données seront bien injecté dans l'entité (entity\user)
    
    if($form->isValid()){
        // Si mon formulaire est bien valide, contrainte spécifié respecté et ce que l'utilisateur a envoyé est correcte
        $user->setRole('ROLE_USER');
        
        $salt = md5(time());
        
        $user->setSalt($salt);
        
        $encodedPassword = $app['security.encoder_factory']
                ->getEncoder($user) //récupère le mdp, utilisation encoder personnalisé
                ->encodePassword($user->getPassword(), $user->getSalt());
        
        $user->setPassword($encodedPassword);
        
        $app['users.dao']->save($user);
        // save : fournit par simpleDAO, fait insert si y'a pas d'id ou un update si il y a un id
        
        return $app->redirect($app['url_generator']->generate('login'));
        //redirect va envoyer une réponse une réponse, il faut la retournée
    }
    
    $formView = $form->createView();
    
     return $app['twig']->render('register.html.twig', ['form' => $formView]);
})
->method('GET|POST') // 2 routes différentes : -pour y accéder et quand -il est valider
->bind('register')
;


$app->error(function (\Exception $e, Request $request, $code) use ($app) {
    if ($app['debug']) {
        return;
    }

    // 404.html, or 40x.html, or 4xx.html, or error.html
    $templates = array(
        'errors/' . $code . '.html.twig',
        'errors/' . substr($code, 0, 2) . 'x.html.twig',
        'errors/' . substr($code, 0, 1) . 'xx.html.twig',
        'errors/default.html.twig',
    );

    return new Response($app['twig']->resolveTemplate($templates)->render(array('code' => $code)), $code);
});
