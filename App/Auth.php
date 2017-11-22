<?php
/**
 * Created by PhpStorm.
 * User: tobirick
 * Date: 16.11.17
 * Time: 18:12
 */

namespace App;
use \App\Models\User;

class Auth {
    // Login user
    public static function login($user) {
        session_regenerate_id(true);

        $_SESSION['user_id'] = $user->id;
    }

    // Logout user
    public static function logout() {
        // Unset all of the session variables.
        $_SESSION = array();

        // If it's desired to kill the session, also delete the session cookie.
        // Note: This will destroy the session, and not just the session data!
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }

        // Finally, destroy the session.
        session_destroy();
    }

    /*
    // check if user is logged in
    public static function isLoggedin() {
        return isset($_SESSION['user_id']);
    }
    */

    // Remember the requested page, to redirect to this page after successfull login
    public static function rememberRequestedPage() {
        $_SESSION['return_to'] = $_SERVER['REQUEST_URI'];
    }

    // Get the originally-requested page to return after requiring login, or default to the index page
    public static function getReturnToPage() {
        return $_SESSION['return_to'] ?? '/';
    }

    // get the current user, if there is one
    public static function getUser() {
        if (isset($_SESSION['user_id'])) {
            return User::findByID($_SESSION['user_id']);
        }
    }
}