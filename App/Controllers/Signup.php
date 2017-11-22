<?php
/**
 * Created by PhpStorm.
 * User: tobirick
 * Date: 15.11.17
 * Time: 20:04
 */

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use \App\Models\User;
use \App\Flash;

class Signup extends \Core\Controller {

    protected function before() {
        parent::before();

        $this->isLoggedIn();
    }

    public function indexAction() {
        View::renderTemplate('Signup/newAccount.html');
    }

    // create user
    public function createAction() {
        $user = new User($_POST);

        if ($user->save()) {

            $user->sendActivationEmail();

            Flash::addMessage('Successfuly signed up. Please activate your account and login.');

            $this->redirect('/login');

            /*
            // Login user after successfully registered
            $login_user = User::authenticate($_POST['email'], $_POST['password']);

            Auth::login($login_user);

            Flash::addMessage('Successfuly signed up');

            $this->redirect(Auth::getReturnToPage());
            */

        } else {

            View::renderTemplate('Signup/newAccount.html', [
                'user' => $user
            ]);

        }
    }

    // activate account page
    public function activateAction() {
        User::activate($this->route_params['token']);

        Flash::addMessage('Successfuly activated your account. You can now login.');

        $this->redirect('/login');
    }
}