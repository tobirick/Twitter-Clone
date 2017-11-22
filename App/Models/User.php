<?php
/**
 * Created by PhpStorm.
 * User: tobirick
 * Date: 15.11.17
 * Time: 20:27
 */

namespace App\Models;

use PDO;
use \App\Token;
use \App\Mail;
use \Core\View;

class User extends \Core\Model {

    public $errors = [];

    public function __construct($data = []) {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        };
    }

    // save user to database
    public function save() {
        $this->validate();

        if (empty($this->errors)) {

            $password_hash = password_hash($this->password, PASSWORD_DEFAULT);

            $token = new Token();
            $hashed_token = $token->getHash();
            $this->activation_token = $token->getValue();

            $sql = 'INSERT INTO users (name, email, password_hash, activation_hash, username)
                    VALUES (:name, :email, :password_hash, :activation_hash, :username)';

            $db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':name', $this->name, PDO::PARAM_STR);
            $stmt->bindValue(':email', $this->email, PDO::PARAM_STR);
            $stmt->bindValue(':username', $this->username, PDO::PARAM_STR);
            $stmt->bindValue(':password_hash', $password_hash, PDO::PARAM_STR);
            $stmt->bindValue(':activation_hash', $hashed_token, PDO::PARAM_STR);

            return $stmt->execute();
        }

        return false;
    }

    // validate user registration
    public function validate() {
        // Name
        if ($this->name == '') {
            $this->errors[] = 'Name is required';
        }

        // email address
        if (filter_var($this->email, FILTER_VALIDATE_EMAIL) === false) {
            $this->errors[] = 'Invalid email';
        }
        if (static::emailExists($this->email, $this->id ?? null)) {
            $this->errors[] = 'email already taken';
        }

        // Username
        if (static::usernameExists($this->username, $this->id ?? null)) {
            $this->errors[] = 'username already taken';
        }
        if (preg_match('/^[a-zA-Z\d]+$/', $this->username) == 0) {
            $this->errors[] = 'Username only availabe with letters and numbers';
        }
        if (strlen($this->username) < 6) {
            $this->errors[] = 'Please enter at least 6 characters for the username';
        }

        // Password
        if(isset($this->password)) {
            if ($this->password != $this->password_confirmation) {
                $this->errors[] = 'Password must match confirmation';
            }

            if (strlen($this->password) < 6) {
                $this->errors[] = 'Please enter at least 6 characters for the password';
            }

            if (preg_match('/.*[a-z]+.*/i', $this->password) == 0) {
                $this->errors[] = 'Password needs at least one letter';
            }

            if (preg_match('/.*\d+.*/i', $this->password) == 0) {
                $this->errors[] = 'Password needs at least one number';
            }
        }
    }

    // Check if email exists, if yes it returns true
    public static function emailExists($email, $ignore_id = null) {
        $user = static::findByEmail($email);

        if($user) {
            if ($user->id != $ignore_id) {
                return true;
            }
        }
        return false;
    }

    // check if username exists, if yes it returns true
    public static function usernameExists($username, $ignore_id = null) {
        $user = static::findByUsername($username);

        if($user) {
            if ($user->id != $ignore_id) {
                return true;
            }
        }
        return false;
    }

    // find user by email
    public static function findByEmail($email) {
        $sql = 'SELECT * FROM users WHERE email = :email';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);

        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        $stmt->execute();

        return $stmt->fetch();
    }

    // find user by username
    public static function findByUsername($username) {
        $sql = 'SELECT * FROM users WHERE username = :username';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindParam(':username', $username, PDO::PARAM_STR);

        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        $stmt->execute();

        return $stmt->fetch();
    }

    // authentice a user by email and password
    public static function authenticate($email, $password) {
        $user = static::findByEmail($email);

        if($user) {
            if(password_verify($password, $user->password_hash)) {
                    return $user;
            }
        }

        return false;
    }

    // find user by id
    public static function findByID($id) {
        $sql = 'SELECT * FROM users WHERE id = :id';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        $stmt->execute();

        return $stmt->fetch();
    }

    // Password reset process
    public static function sendPasswordReset($email) {
        $user = static::findByEmail($email);

        if ($user) {
            // start password reset process here
            if($user->startPasswordReset()) {
                // send email here
                $user->sendPasswordResetEmail();
            }

        }
    }

    protected function startPasswordReset() {
        $token = new Token();
        $hashed_token = $token->getHash();
        $this->password_reset_token = $token->getValue();

        $expiry_timestamp = time() + 60*60*2; // 2 hours from now

        $sql = "Update users
                SET password_reset_hash = :token_hash,
                    password_reset_expires_at = :expires_at
                Where id = :id";

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':token_hash', $hashed_token, PDO::PARAM_STR);
        $stmt->bindValue(':expires_at', date('Y-m-d H:i:s', $expiry_timestamp), PDO::PARAM_STR);
        $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    protected function sendPasswordResetEmail() {
        $url = 'http://' . $_SERVER['HTTP_HOST'] . '/password/reset/' . $this->password_reset_token;

        $text = View::getTemplate('Emails/reset_email.txt', ['url' => $url]);
        $html = View::getTemplate('Emails/reset_email.html', ['url' => $url]);

        Mail::send($this->email, 'Password reset', $text, $html);
    }

    // find user by password reset token
    public static function findByPasswordReset($token) {
        $token = new Token($token);
        $hashed_token = $token->getHash();

        $sql = "SELECT * FROM users
                WHERE password_reset_hash = :token_hash";

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':token_hash', $hashed_token, PDO::PARAM_STR);

        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        $stmt->execute();

        $user = $stmt->fetch();

        if($user) {
            // check password reset token hasn't expired
            if(strtotime($user->password_reset_expires_at) > time()) {
                return $user;
            }
        }
    }

    // reset password
    public function resetPassword($password, $password_confirmation) {
        $this->password = $password;
        $this->password_confirmation = $password_confirmation;

        $this->validate();

        if (empty($this->errors)) {
            $password_hash = password_hash($this->password, PASSWORD_DEFAULT);

            $sql = "UPDATE USERS
                    SET password_hash = :password_hash,
                        password_reset_hash = NULL,
                        password_reset_expires_at = NULL
                    WHERE id = :id";

            $db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':password_hash', $password_hash, PDO::PARAM_STR);
            $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);

            return $stmt->execute();
        }

        return false;
    }

    // send account activation email
    public function sendActivationEmail() {
        $url = 'http://' . $_SERVER['HTTP_HOST'] . '/signup/activate/' . $this->activation_token;

        $text = View::getTemplate('Emails/activation_email.txt', ['url' => $url]);
        $html = View::getTemplate('Emails/activation_email.html', ['url' => $url]);

        Mail::send($this->email, 'Account activation', $text, $html);
    }

    // activate account
    public static function activate($value) {
        $token = new Token($value);
        $hashed_token = $token->getHash();

        $sql = 'UPDATE users
                SET is_active=1,
                    activation_hash = null
                WHERE activation_hash = :hashed_token';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':hashed_token', $hashed_token, PDO::PARAM_STR);

        $stmt->execute();
    }

    // update user profile
    public function updateProfile($data) {
        $this->name = $data['name'];
        $this->email = $data['email'];
        $this->username = $data['username'];
        $this->website = $data['website'];
        $this->bio = $data['bio'];

        // only validate and update the password if a value provided
        if($data['password'] != '') {
            $this->password = $data['password'];
            $this->password_confirmation = $data['password_confirmation'];
        }

        $this->validate();

        if(empty($this->errors)) {
            $sql = 'UPDATE users
                    SET name = :name,
                        email = :email,
                        username = :username,
                        website = :website,
                        bio = :bio';

            if (isset($this->password)) {
            $sql .= ', password_hash = :password_hash';
            }

            $sql .= "\nWHERE id = :id";

            $db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':name', $this->name, PDO::PARAM_STR);
            $stmt->bindValue(':email', $this->email, PDO::PARAM_STR);
            $stmt->bindValue(':username', $this->username, PDO::PARAM_STR);
            $stmt->bindValue(':website', $this->website, PDO::PARAM_STR);
            $stmt->bindValue(':bio', $this->bio, PDO::PARAM_STR);
            $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);

            if (isset($this->password)) {
                $password_hash = password_hash($this->password, PASSWORD_DEFAULT);
                $stmt->bindValue(':password_hash', $password_hash, PDO::PARAM_STR);
            }

            return $stmt->execute();
        }

        return false;
    }

    // search users
    public static function searchUsersByUsername($search) {
        $sql = 'SELECT name, username  FROM users WHERE username LIKE ? OR name LIKE ?';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue('1', '%'.$search.'%', PDO::PARAM_STR);
        $stmt->bindValue('2', '%'.$search.'%', PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
}