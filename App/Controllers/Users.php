<?php
/**
 * Created by PhpStorm.
 * User: tobirick
 * Date: 20.11.17
 * Time: 13:54
 */

namespace App\Controllers;

use App\Models\Follow;
use App\Models\User;
use Core\View;

class Users extends \Core\Controller {

    public function indexAction() {
        $username = $this->route_params['user'];

        $user = User::findByUsername($username);

        $followers = Follow::findFollowerByID($user->id);

        $following = Follow::findFollowingByID($user->id);

        if($user) {
            View::renderTemplate('Users/index.html.twig', [
                'user' => $user,
                'followers' => $followers,
                'following' => $following
            ]);
        } else {
            View::renderTemplate('Users/userNotFound.html.twig');
        }
    }
}