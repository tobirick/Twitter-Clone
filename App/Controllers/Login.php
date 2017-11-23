<?php
/**
 * Created by PhpStorm.
 * User: tobirick
 * Date: 16.11.17
 * Time: 16:38
 */

namespace App\Controllers;

use \Core\View;
use \App\Models\User;
use \App\Auth;
use \App\Flash;

class Login extends \Core\Controller {

    public function indexAction() {
        if(!$this->isLoggedIn()) {
            View::renderTemplate('Login/loginAccount.html.twig');
        }
    }

    // Login user
    public function createAction() {
        $user = User::authenticate($_POST['email'], $_POST['password']);

        if($user) {

            if($user->is_active == 1) {

            Auth::login($user);

            Flash::addMessage('Login successful');

            //$this->redirect('');
            $this->redirect(Auth::getReturnToPage());

            } else {
                Flash::addMessage('Please activate your account. ', Flash::WARNING);

                View::renderTemplate('Login/loginAccount.html.twig', [
                    'email' => $_POST['email'],
                ]);
            }

        } else {

            Flash::addMessage('Login unsuccessful, please try again.', Flash::WARNING);

            View::renderTemplate('Login/loginAccount.html.twig', [
                'email' => $_POST['email'],
            ]);
        }
    }

    public function destroyAction() {
        Auth::logout();

        $this->redirect('/login/show-logout-message');

    }

    // we need this because we destroy the session in the logout action, so the flash messages gets also destroyed
    public function showLogoutMessageAction() {

        Flash::addMessage('Logout successful');

        $this->redirect('/login');

    }
}