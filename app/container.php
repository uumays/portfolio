<?php

use App\Helper\Session;

$container = $app->getContainer();

$container['debug'] = function(){
    return true;
};

$container['csrf'] = function () {
    return new \Slim\Csrf\Guard;
};

$container['view'] = function ($container) {
    $dir = dirname(__DIR__);
    $view = new \Slim\Views\Twig($dir. '/app/views', [
        'cache' => $container->debug ? false : $dir.'/tmp/cache',
        'debug' => $container->debug
    ]);
    if($container->debug){
        $view->addExtension(new Twig_Extension_Debug());
    }
    // Instantiate and add Slim specific extension
    $router = $container->get('router');
    $uri = \Slim\Http\Uri::createFromEnvironment(new \Slim\Http\Environment($_SERVER));
    $view->addExtension(new Slim\Views\TwigExtension($router, $uri));

    return $view;
};

$container['mailer'] = function($container){
    if($container->debug){
        $transport = new Swift_SmtpTransport('localhost', 1025);
    } else {
        $transport = new Swift_MailTransport();
    }

  $mailer = new Swift_Mailer($transport);
  return $mailer;
};

$container['environment'] = function(){
    $scriptName = $_SERVER['SCRIPT_NAME'];
    $_SERVER['SCRIPT_NAME'] = dirname(dirname($scriptName)) . '/' . basename($scriptName);
    return new Slim\Http\Environment($_SERVER);
};

$container['session'] = function (){
    return new Session();
};