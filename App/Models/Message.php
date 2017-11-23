<?php
/**
 * Created by PhpStorm.
 * User: tobirick
 * Date: 22.11.17
 * Time: 19:57
 */

namespace App\Models;

use PDO;

class Message extends \Core\Model {
    public static function findRecentMessages($userid) {
        $sql = "SELECT * FROM messages
                LEFT JOIN users ON messages.from_id = users.id
                WHERE messages.to_id = :userid";

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':userid',$userid, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
}