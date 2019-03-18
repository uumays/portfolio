<?php

use App\Controllers\PagesController;
use App\Controllers\UserController;

require '../vendor/autoload.php';
// Initialisation de la session
session_start();

$app = new \Slim\App([
    'settings' => [
        'displayErrorDetails' => true
    ]
]);

// Container

require('../app/container.php');

$container = $app->getContainer();

//MiddleWare

$app->add(new \App\MiddleWares\SessionMiddleWare($container->view->getEnvironment()));
$app->add(new \App\MiddleWares\EmailMiddleWare($container->view->getEnvironment()));
$app->add(new \App\MiddleWares\FlashMiddleWare($container->view->getEnvironment()));
$app->add(new \App\MiddleWares\OldMiddleWare($container->view->getEnvironment()));
$app->add(new \App\MiddleWares\TwigCsrfMiddleWare($container->view->getEnvironment(), $container->csrf));
$app->add($container->csrf);

// Liste des appelles de fonction venant des controllers
$app->get('/connexion', PagesController::class. ':getConnexion')->setName('connexion');
$app->post('/connexion', UserController::class. ':postConnexion');
$app->get('/', PagesController::class . ':home')->setName('home');
$app->get('/me-contacter', PagesController::class . ':getContact')->setName('contact');
$app->get('/projets', PagesController::class . ':getProjet')->setName('projet');
$app->get('/veille-techno', PagesController::class. ':getVeille')->setName('veille');
$app->post('/me-contacter', PagesController::class . ':postContact');
$app->get('/lien-externe',PagesController::class. ':lienExterne')->setName('lienExterne');
$app->get('/inscription', PagesController::class. ':getInscription')->setName('inscription');
$app->get('/monEspace', UserController::class. ':getMonEspace')->setName('monEspace');
$app->post('/inscription', UserController::class. ':postInscription');
$app->get('/deconnexion', UserController::class . ':deconnexion')->setName('deconnexion');

$app->run();