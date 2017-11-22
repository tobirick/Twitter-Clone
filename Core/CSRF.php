<?php
/**
 * Created by PhpStorm.
 * User: tobirick
 * Date: 17.11.17
 * Time: 16:34
 */

namespace Core;

/**
 * CSRF protection
 */
class CSRF
{
    /**
     * Get and store a new CSRF token
     *
     * @return string  A 32-character token
     */
    public static function getToken()
    {
        $token = new \App\Token();
        $value = $token->getValue();
        $_SESSION["csrf_token"] = $value;

        return $value;
    }

    /**
     * Check the token value
     */
    public static function checkToken()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if ( ! isset($_POST['csrf_token'])) {
                header('HTTP/1.0 403 Forbidden');
                exit('Missing CSRF token');
            }

            // Get the token from the session and remove it
            $token = $_SESSION['csrf_token'] ?? '';
            unset($_SESSION['csrf_token']);

            if ($_POST['csrf_token'] != $token) {
                header('HTTP/1.0 403 Forbidden');
                exit('Invalid CSRF token');
            }
        }
    }
}