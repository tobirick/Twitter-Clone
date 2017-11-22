<?php
/**
 * Created by PhpStorm.
 * User: tobirick
 * Date: 15.11.17
 * Time: 13:36
 */

namespace Core;

use App\Models\Follow;

class View {
    public static function render($view, $args = []) {
        extract($args, EXTR_SKIP);

        $file = "../App/Views/$view";

        if (is_readable($file)) {
            require $file;
        } else {
            //echo "$file not found";
            throw new \Exception("$file not found");
        }
    }

    // echo the template
    public static function renderTemplate($template, $args = []) {
        // CSRF token
        $args['csrf_token'] = CSRF::getToken();

        echo static::getTemplate($template, $args);
    }

    // get template
    public static function getTemplate($template, $args = []) {
        static $twig = null;

        if($twig === null) {
            $loader = new \Twig_Loader_Filesystem(dirname(__DIR__) . '/App/Views');
            $twig = new \Twig_Environment($loader);
            //$twig->addGlobal('session', $_SESSION);
            // Check if user is logged in
            //$twig->addGlobal('is_logged_in', \App\Auth::isLoggedin());
            // If user is logged in, get user data
            $twig->addGlobal('current_user', \App\Auth::getUser());
            // Flash Messages
            $twig->addGlobal('flash_messages', \App\Flash::getMessages());
            // CSRF Token
            //$twig->addGlobal('csrf_token', CSRF::getToken());

            // Check If user Follows
            $checkIfUserFollows = new \Twig_Function('checkIfUserFollows', function ($follower_id, $user_id) {
                return Follow::checkIfUserFollows($follower_id, $user_id);
            });
            $twig->addFunction($checkIfUserFollows);

        }

        return $twig->render($template, $args);
    }
}