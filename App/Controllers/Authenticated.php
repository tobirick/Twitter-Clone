<?php
/**
 * Created by PhpStorm.
 * User: tobirick
 * Date: 16.11.17
 * Time: 18:37
 */

namespace App\Controllers;

abstract class Authenticated extends \Core\Controller {
    // requires login for all action method in a controller
    protected function before() {

        parent::before();

        $this->requireLogin();

    }
}