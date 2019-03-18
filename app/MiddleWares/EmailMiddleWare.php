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

class EmailMiddleWare
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
        $this->twig->addGlobal('email', isset($_SESSION['email']) ? $_SESSION['email'] : []);

        $response = $next($request, $response);
        if ($response->getStatusCode() === 400){
            $_SESSION['email'] = $request->getParams();
        }

        return $response;
    }
}