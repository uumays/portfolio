<?php

namespace App\Controllers;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Respect\Validation\Validator;

class PagesController extends Controller {

    /**
     * @param RequestInterface $request
     * @param ResponseInterface $response
     */
    public function home(RequestInterface $request, ResponseInterface $response){
        $this->render($response,'pages/home.twig');
    }

    /**
     * @param RequestInterface $request
     * @param ResponseInterface $response
     */
    public function getContact(RequestInterface $request, ResponseInterface $response){
        return $this->render($response, 'pages/contact.twig');

    }

    /**
     * @param RequestInterface $request
     * @param ResponseInterface $response
     */
    public function lienExterne(RequestInterface $request, ResponseInterface $response){
        return $this->render($response, 'pages/lienExterne.twig');
    }

    /**
     * @param RequestInterface $request
     * @param ResponseInterface $response
     */
    public function getInscription(RequestInterface $request, ResponseInterface $response){
        return $this->render($response, 'pages/inscription.twig');
    }

    /**
     * @param RequestInterface $request
     * @param ResponseInterface $response
     */
    public function getProjet(RequestInterface $request, ResponseInterface $response){
        return $this->render($response, 'pages/projet.twig');
    }

    public function getConnexion(RequestInterface $request, ResponseInterface $response){
        return $this->render($response, 'pages/connexion.twig');
    }

    /**
     * @param RequestInterface $request
     * @param ResponseInterface $response
     */
    public function getVeille(RequestInterface $request, ResponseInterface $response){
        return $this->render($response, 'pages/veille.twig');
    }

    /**
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @return mixed
     */
    public function postContact(RequestInterface $request, ResponseInterface $response){
        $errors = [];
        Validator::email()->validate($request->getParam('email')) || $errors['email'] = 'Votre email n\'est pas valide';
        Validator::notEmpty()->validate($request->getParam('name')) || $errors['name'] = 'Veuillez entrer votre nom';
        Validator::notEmpty()->validate($request->getParam('content')) || $errors['content'] = 'Veuillez écrire un message';
        if(empty($errors)){
            $message = (new \Swift_Message())
                ->setSubject('Message de contact')
                ->setFrom([$request->getParam('email') => $request->getParam('name')])
                //Adresse mail cible
                ->setTo('contact@test.fr')
                ->setBody("un email vous a été envoyé :
            {$request->getParam('content')}");
            $this->mailer->send($message);
            $this->flash('Votre message a bien été envoyé');
            return $this->redirect($response,'contact');
        } else {
            $this->flash('Certains champs n\'ont pas été rempli correctement. ', 'error');
            $this->flash($errors, 'errors');
            return $this->redirect($response,'contact', 400);
        }

    }
}
