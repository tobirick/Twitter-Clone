<?php
/**
 * Created by PhpStorm.
 * User: tobirick
 * Date: 22.11.17
 * Time: 20:07
 */

namespace App\Controllers;

use App\Auth;
use App\Models\Message;
use \Core\View;
use \Core\CSRF;

class Messages extends \Core\Controller {

    public function recentMessagesAction() {
        if(isset($_POST['showMessage']) && !empty($_POST['showMessage'])) {
            $userid = Auth::getUser()->id;

            $messages = Message::findRecentMessages($userid);

            $html = View::getTemplate('Modals/messagesModal.html.twig', [
                'messages' => $messages
            ]);

            $result['html'] = $html;

            if($result) {
                $csrf_token = CSRF::getToken();
                $result['csrf_token'] = $csrf_token;
            }

            header('Content-type: application/json');
            echo json_encode($result);
        }
    }
}