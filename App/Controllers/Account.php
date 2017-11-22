<?php
/**
 * Created by PhpStorm.
 * User: tobirick
 * Date: 16.11.17
 * Time: 15:49
 */

namespace App\Controllers;

    use \App\Models\User;

class Account extends \Core\Controller {

    // check if email exists for jquery validation plugin
    public function validateEmailAction() {
        $is_valid = ! User::emailExists($_GET['email'], $_GET['ignore_id'] ?? null);

        header('Content-type: application/json');
        echo json_encode($is_valid);
    }

    public function validateUsernameAction() {
        $is_valid = ! User::usernameExists($_GET['username'], $_GET['ignore_id'] ?? null);

        header('Content-type: application/json');
        echo json_encode($is_valid);
    }
}