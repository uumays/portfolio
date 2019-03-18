<?php

namespace App\Controllers;

use PDO;
use Psr\Http\Message\ResponseInterface;

class Controller{
    private $container;

    /**
     * PagesController constructor.
     * @param $container
     */
    public function __construct($container)
    {
        $this->container = $container;
    }

    /**
     * initialisation de la connexion à la base de données
     * @return PDO
     */
    public function pdo(){
        $pdo = new PDO('mysql:dbname=portfolio;host=localhost', 'root', '');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    }

    /**
     * rendu de la vue
     * @param ResponseInterface $response
     * @param $file
     * @param array $params
     */
    public function render(ResponseInterface $response, $file, $params = []){
        $this->container->view->render($response,$file, $params);
    }

    /**
     * redirection
     * @param $response
     * @param $name
     * @param int $status
     * @return mixed
     */
    public function redirect($response, $name, $status = 302){
        return $response->withStatus($status)->withHeader('Location', $this->router->pathFor($name));
    }

    /**
     * message flash pour les formulaires
     * @param $message
     * @param string $type
     * @return mixed
     */
    public function flash($message, $type = 'success' ){
        if(!isset($_SESSION['flash'])){
            $_SESSION['flash'] = [];
        }
        return $_SESSION['flash'][$type] = $message;
    }

    /**
     * @param $name
     * @return mixed
     */
    public function __get($name)
    {
        return $this->container->get($name);
    }


}