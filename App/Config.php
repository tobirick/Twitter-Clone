<?php
/**
 * Created by PhpStorm.
 * User: tobirick
 * Date: 15.11.17
 * Time: 15:45
 */
namespace App;

class Config {
    // database settings
    const DB_HOST = 'localhost';
    const DB_NAME = 'mvccms';
    const DB_USER = 'root';
    const DB_PASSWORD = 'root';

    // show or hide error messages
    const SHOW_ERRORS = true;
    const SECRET_KEY = 'iggRpwAN8fkkST60TOBjm59LWmrcoJdu';

    // Mailgun
    const MAILGUN_API_KEY = "key-88969e12588aa89f692a65cf7c86d142";
    const MAILGUN_DOMAIN = "sandbox2bb83bf3dc2d46a7b70b2d587f436593.mailgun.org";
}