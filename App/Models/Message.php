<?php
/**
 * Created by PhpStorm.
 * User: tobirick
 * Date: 22.11.17
 * Time: 19:57
 */

namespace App\Models;

use App\Auth;
use PDO;

class Message extends \Core\Model {

    public function __construct($data = []) {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        };
    }

    // find recent messages
    public static function findRecentMessages($userid) {
        /*
        $sql = "SELECT * FROM messages
                LEFT JOIN users ON messages.from_id = users.id
                WHERE messages.to_id = :userid";

*/



        $sql = "SELECT messages.message, messages.from_id, messages.to_id, messages.time, users.username, users.name FROM messages
                LEFT JOIN users ON messages.from_id = users.id
                AND messages.id IN (SELECT max(messages.id) FROM messages WHERE messages.from_id = users.id)
                WHERE messages.to_id = :userid and messages.from_id = users.id
                GROUP BY users.id
                ORDER BY messages.id DESC";


        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':userid',$userid, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    // get messages from one conversation
    public static function findAllConversationMessages($from_id, $userid) {
        $sql = "SELECT * FROM messages
                LEFT JOIN users ON messages.from_id = users.id
                WHERE (messages.from_id = :from_id AND messages.to_id = :userid)
                OR (messages.to_id = :from_id AND messages.from_id = :userid)";

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':from_id', $from_id, PDO::PARAM_INT);
        $stmt->bindValue(':userid', $userid, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    // send message
    public function send() {
        $userid = Auth::getUser()->id;

        $sql = "INSERT INTO messages (message, from_id, to_id, time)
                VALUES(:message, :from_id, :to_id, :time)";

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':message',$this->msg, PDO::PARAM_STR);
        $stmt->bindValue(':from_id', $userid, PDO::PARAM_INT);
        $stmt->bindValue(':to_id', $this->to_id, PDO::PARAM_INT);
        $stmt->bindValue(':time',date('Y-m-d H:i:s', time()), PDO::PARAM_STR);

        return $stmt->execute();
    }
}