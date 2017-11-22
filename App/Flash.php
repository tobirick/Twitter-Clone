<?php
/**
 * Created by PhpStorm.
 * User: tobirick
 * Date: 16.11.17
 * Time: 22:46
 */

namespace App;

class Flash {

    CONST SUCCESS = 'success';
    CONST INFO = 'info';
    CONST WARNING = 'warning';


    public static function addMessage($message, $type = 'success') {
        // Create array in the session if it doenst already exist
        if (!isset($_SESSION['flash_notifications'])) {
            $_SESSION['flash_notifications'] = [];
        }

        // append the message to the array
        $_SESSION['flash_notifications'][] = [
            'body' => $message,
            'type' => $type
        ];
    }

    public static function getMessages() {
        if (isset($_SESSION['flash_notifications'])) {
            $messages = $_SESSION['flash_notifications'];
            unset($_SESSION['flash_notifications']);

            return $messages;
        }
    }
}