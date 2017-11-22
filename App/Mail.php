<?php
/**
 * Created by PhpStorm.
 * User: tobirick
 * Date: 18.11.17
 * Time: 13:28
 */

namespace App;

use Mailgun\Mailgun;

class Mail {
    // send a email
    public static function send($to, $subject, $text, $html) {
        $mg = Mailgun::create(Config::MAILGUN_API_KEY);

        # Now, compose and send your message.
        # $mg->messages()->send($domain, $params);
        $mg->messages()->send(Config::MAILGUN_DOMAIN, [
            'from'    => 'bob@example.com',
            'to'      => $to,
            'subject' => $subject,
            'text'    => $text,
            'html'    => $html
        ]);
    }
}