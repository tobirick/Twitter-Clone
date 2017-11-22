<?php
/**
 * Created by PhpStorm.
 * User: tobirick
 * Date: 20.11.17
 * Time: 13:39
 */

namespace App\Models;

use PDO;

class Follow extends \Core\Model {

    // check if user follows, follower_id = follower; user_id = followed by followeder_d
    public static function checkIfUserFollows($follower_id, $user_id) {
        $sql = "SELECT user_id FROM follows
                WHERE follower_id = :follower_id AND user_id = :user_id";

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':follower_id', $follower_id, PDO::PARAM_INT);
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);

        $stmt->execute();

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if($result) {
            return true;
        } else {
            return false;
        }
    }

    // follow user
    public static function followUser($follower_id, $user_id) {
        $followed_at = time();

        $sql = "INSERT INTO follows (user_id, follower_id, followed_at)
                VALUES (:user_id, :follower_id, :followed_at)";

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindValue(':follower_id', $follower_id, PDO::PARAM_INT);
        $stmt->bindValue(':followed_at', date('Y-m-d H:i:s', $followed_at), PDO::PARAM_STR);

        return $stmt->execute();

    }

    // unfollow user
    public static function unfollowUser($follower_id, $user_id) {
        $sql = "DELETE FROM follows WHERE user_id = :user_id AND follower_id = :follower_id";

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindValue(':follower_id', $follower_id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    // get follower from user
    public static function findFollowerByID($userid) {
        $sql = "SELECT users.username, users.name, users.id FROM users INNER JOIN follows ON users.id = follows.follower_id WHERE follows.user_id = :userid";

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':userid', $userid, PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // get followed user from user
    public static function findFollowingByID($userid) {
        $sql = "SELECT users.username, users.name, users.id FROM users INNER JOIN follows ON users.id = follows.user_id WHERE follows.follower_id = :userid";

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':userid', $userid, PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}