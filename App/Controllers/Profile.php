<?php
/**
 * Created by PhpStorm.
 * User: tobirick
 * Date: 19.11.17
 * Time: 18:02
 */

namespace App\Controllers;

use App\Auth;
use App\Flash;
use \Core\View;

class Profile extends Authenticated {
    protected function before() {

        parent::before();

        $this->user = Auth::getUser();
    }

    public function editAction() {
        View::renderTemplate('Profile/edit.html.twig', [
            'user' => $this->user
        ]);
    }

    public function updateAction() {

        if ($this->user->updateProfile($_POST)) {

            Flash::addMessage('Profile changed successfully.');

            $this->redirect('/users/' . $this->user->username);

        } else {

            View::renderTemplate('Profile/edit.html.twig', [
                'user' => $this->user
            ]);

        }
    }
}