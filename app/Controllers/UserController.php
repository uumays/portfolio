<?php

namespace App\Controllers;

use App\Helper\Session;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Respect\Validation\Validator;

class UserController extends Controller {
    /**
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @return mixed
     */
    public function postInscription(RequestInterface $request, ResponseInterface $response){
        $errors = [];
        Validator::email()->validate($request->getParam('email')) || $errors['email'] = 'Votre email n\'est pas valide';
        Validator::notEmpty()->validate($request->getParam('login')) || $errors['login'] = 'Veuillez entrer votre login';
        Validator::notEmpty()->validate($request->getParam('mdp')) || $errors['mdp'] = 'Veuillez entrer un mot de passe';
        // la fonction "flash" est dans le controller c'est juste une notif
        if(empty($errors)){
            $prepare = $this->pdo()->prepare('INSERT INTO users(login, email, mdp) VALUES ("'.htmlspecialchars($request->getParam('login')).'", "'.$request->getParam('email').'", "'.hash('md5', $request->getParam('mdp')).'")');
            $req = $prepare->execute();
            $this->flash('Vous êtes bien inscrit');
            return $this->redirect($response, 'home');
        } else {
            // Message d'erreur lorsqu'un des champs n'est pas rempli correctement
            $this->flash('Certains champs n\'ont pas été rempli correctement ', 'error');
            $this->flash($errors, 'errors');
            return $this->redirect($response,'incription', 400);
        }
    }

    /**
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @return mixed
     */
    public function supprimerCompte(RequestInterface $request, ResponseInterface $response){
        if(isset($_SESSION['login'])){
            $prepare = $this->pdo()->prepare('DELETE FROM users WHERE login="'.$_SESSION['login'].'"');
            $req = $prepare->execute();
            unset($_SESSION['login']);
            $this->flash('Votre compte a bien été supprimé');
            return $this->redirect($response, 'home');
        }
    }

    /**
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @return mixed
     */
    public function postConnexion(RequestInterface $request, ResponseInterface $response){
        $login = $request->getParam('login');
        $mdp = hash('md5', $request->getParam('mdp'));
        $prepare = $this->pdo()->prepare('SELECT login, mdp FROM users WHERE login="'.$login.'" AND mdp="'.$mdp.'"');
        $req = $prepare->execute();
        if(isset($req)){
            $session = new Session();
            $session->set('login', $login);
            $session->get('login');
            return $this->redirect($response, 'home');
        } else {
            $this->flash('Login ou mot de passe incorrect', 'error');
            return $this->redirect($response, 'connexion', 400);
        }
    }

    /**
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @return mixed
     */
    public function deconnexion(RequestInterface $request, ResponseInterface $response){
        if(isset($_SESSION['login'])){
            unset($_SESSION['login']);
            unset($_SESSION['email']);
        }
        return $this->redirect($response, 'home');
    }

    /**
     * @param RequestInterface $request
     * @param ResponseInterface $response
     */
    public function getMonEspace(RequestInterface $request, ResponseInterface $response){
        $prepare = 'SELECT email FROM users WHERE login = "'.$_SESSION['login'].'"';
        $req = $this->pdo()->query($prepare);
        while($donnees = $req->fetch()){
            $mail = $donnees['email'];
            $_SESSION['email'] = $mail;
        }
        return $this->render($response, 'pages/monEspace.twig');
    }
}