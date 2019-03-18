<?php
/**
 * Created by PhpStorm.
 * User: Ludovic
 * Date: 13/03/2019
 * Time: 08:48
 */

namespace App\MiddleWares;


use Slim\Http\Request;
use Slim\Http\Response;

class SessionMiddleWare
{
    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * SessionMiddleWare constructor.
     * @param $twig
     */
    public function __construct(\Twig_Environment $twig)
    {
        $this->twig = $twig;
    }

    public function __invoke(Request $request,Response $response, $next)
    {
        $this->twig->addGlobal('login', isset($_SESSION['login']) ? $_SESSION['login'] : []);

        $response = $next($request, $response);
        if ($response->getStatusCode() === 400){
            $_SESSION['login'] = $request->getParams();
        }

        return $response;
    }
}