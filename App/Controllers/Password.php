<?php
/**
 * Created by PhpStorm.
 * User: tobirick
 * Date: 18.11.17
 * Time: 13:39
*/

namespace App\Controllers;

use \Core\View;
use \App\Models\User;
use \App\Flash;

class Password extends \Core\Controller {

    protected function before() {
        parent::before();

        $this->isLoggedIn();
    }

    public function forgotAction() {
        View::renderTemplate('Password/forgot.html');
    }

    public function requestResetAction() {
        User::sendPasswordReset($_POST['email']);

        View::renderTemplate('Password/resetRequested.html');
    }

    public function resetAction() {
        $token = $this->route_params['token'];

        $user = $this->getUserOrExit($token);

        View::renderTemplate('Password/reset.html', [
            'token' => $token
        ]);

    }

    public function resetPasswordAction() {
        $token = $_POST['reset_token'];

        $user = $this->getUserOrExit($token);

       if($user->resetPassword($_POST['password'], $_POST['password_confirmation'])) {
           Flash::addMessage("Successfuly reset password. You can now log in.");


           $this->redirect('/login');
       } else {
           View::renderTemplate('Password/reset.html', [
              'token' => $token,
              'user' => $user
           ]);

       }
    }

    protected function getUserOrExit($token) {
        $user = User::findByPasswordReset($token);

        if($user) {
            return $user;
        } else {
            View::renderTemplate('Password/tokenExpired.html');
            exit;
        }
    }
}