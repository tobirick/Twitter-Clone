<?php
/**
 * Created by PhpStorm.
 * User: tobirick
 * Date: 14.11.17
 * Time: 20:49
 */

namespace App\Controllers;

use \Core\View;

class Home extends \Core\Controller {
    public function indexAction() {

        View::renderTemplate('Home/index.html');

        /*View::render('Home/index.php', [
            'name' => 'Tobi',
            'colours' => ['red', 'green', 'blue']
        ]);
        */
    }

    protected function before() {
        // check if user logged in or something like that bruuuuuh
    }

    protected function after() {
        // after for example indexAction is called
    }
}