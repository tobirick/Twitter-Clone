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

    // get all recent messages and show it in popup
    public function recentMessagesAction() {
        if(isset($_POST['showMessage']) && !empty($_POST['showMessage'])) {
            $userid = Auth::getUser()->id;

            $messages = Message::findRecentMessages($userid);

            $html = View::getTemplate('Modals/messagesListModal.html.twig', [
                'messages' => $messages
            ]);

            $result['html'] = $html;

            if($result) {
                $csrf_token = CSRF::getToken();
                $result['csrf_token'] = $csrf_token;
            }

            header('Content-type: application/json');
            echo json_encode($result);
            //var_dump($messages);
        }
    }

    // get messages and show it in popup
    public function messagesConversationAction() {
        if(isset($_POST['from_id']) && !empty($_POST['from_id'])) {
            $from_id = $_POST['from_id'];
            $userid = Auth::getUser()->id;

            $messages = Message::findAllConversationMessages($from_id, $userid);

            $html = View::getTemplate('Modals/messagesConversation.html.twig', [
                'messages' => $messages,
                'chatpartnerid' => $_POST['from_id']
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

    public function sendMessageAction() {
        if(isset($_POST['to_id']) && !empty($_POST['to_id'])) {
            $message = new Message($_POST);

            if(!empty($message->msg)) {
                if($message->send()) {

                    $csrf_token = CSRF::getToken();
                    $result['csrf_token'] = $csrf_token;

                    header('Content-type: application/json');
                    echo json_encode($result);

                }
            }
        }
    }
}