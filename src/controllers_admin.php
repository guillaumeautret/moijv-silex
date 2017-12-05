<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

$app->get('/loginadmin', function(Request $request) use ($app) {
    return $app['twig']->render('admin/login_admin.html.twig', array(
        'error'         => $app['security.last_error']($request),
        'last_username' => $app['session']->get('_security.last_username'),
    ));
})->bind('loginadmin')
;

$adminGroup = $app['controllers_factory'];

$adminGroup->get('dashboard', function() use($app){
    // afficher template twig
    return $app['twig']->render('admin/dashboard.html.twig');
    
})->bind('admin_dashboard')
;

$adminGroup->get('userlist', function() use ($app){
    $users = $app['users.dao']->findMany();
    return $app['twig']->render('admin/userlist.html.twig', [
        'users' => $users
    ]);
    // 2eme parametre est un tableau associatif; 
    // A l'inteérieur du tableau nous disons que dans la variable user ('users') il y a aura les données utilisateurs ($users)
})->bind('admin_userlist')
;

$adminGroup->get('/userdelete/{id}', function($id) use ($app){
    // Les paramètres dynamiques sur silex se fait entre accolades.
    // quand on a un parametre dynamique injécté telle quelle. le nom du parametre dynamique est transmis en argument dans la fonction
    $user = $app['users.dao']->find($id);
    // findOne : select avec un fecth
    // findMany : select avec fetchall
    // find : retourne un résultat avec l'id en question.
    
    $app['users.dao']->delete($user);
    return $app->redirect($app['url_generator']->generate('admin_userlist'));
    
})
->bind('admin_userdelete')
;

$adminGroup->match('/useredit/{id}', function (Request $request, $id) use ($app){
    // match : permet matcher plusieurs méthodes d'un coup, et peut filtrer cette méthode à la fin (ici get et post) 
    $user = $app['users.dao']->find($id);
    // créer formulaire a aprtir de usertype
    
    $form = $app['form.factory']->createBuilder(\FormType\UserType::class, $user)
            ->remove('password') // methode de createBuilder, formBuilder : permet de créer et enlever des champs
            ->add('submit', SubmitType::class, [
            'label' => 'Enregistrer'
        ])
            ->getForm();
    
    $form->handleRequest($request);
    
    if($form->isValid()){
        // Si mon formulaire est bien valide, contrainte spécifié respecté et ce que l'utilisateur a envoyé est correcte
        
        
        $app['users.dao']->save($user);
        // save : fournit par simpleDAO, fait insert si y'a pas d'id ou un update si il y a un id
        
        return $app->redirect($app['url_generator']->generate('admin_userlist'));
    }
    
    $formView = $form->createView();
    
     return $app['twig']->render('admin/useredit.html.twig', ['form' => $formView]);
})
->bind('admin_useredit')
->method('GET|POST');

$app->mount('/admin', $adminGroup);
// montage : je monte admingroup sur cette url

