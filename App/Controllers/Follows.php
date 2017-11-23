<?php
/**
 * Created by PhpStorm.
 * User: tobirick
 * Date: 21.11.17
 * Time: 20:21
 */

namespace App\Controllers;

use App\Auth;
use App\Models\Follow;
use Core\CSRF;

class Follows extends \Core\Controller {

    public function followUserAction() {

        if(isset($_POST['user_id']) && !empty($_POST['user_id'])) {

            if(Follow::checkIfUserFollows(Auth::getUser()->id, $_POST['user_id'])) {
                // delete follow
                Follow::unfollowUser(Auth::getUser()->id, $_POST['user_id']);

                $result['follow'] = 'follow';

            } else {
                // follow user
                Follow::followUser(Auth::getUser()->id, $_POST['user_id']);

                $result['follow'] = 'unfollow';
            }

            if($result) {
                $csrf_token = CSRF::getToken();

                $result['csrf_token'] = $csrf_token;
            }


            header('Content-type: application/json');
            echo json_encode($result);

        }
    }
}