<?php
/**
 * Created by PhpStorm.
 * User: tobirick
 * Date: 20.11.17
 * Time: 18:44
 */

namespace App\Controllers;

use App\Models\User;
use Core\View;

class Search extends \Core\Controller {
    public function searchUserAction() {

        if(isset($_GET['search']) && !empty($_GET['search'])) {

            $search = $_GET['search'];

            $result = User::searchUsersByUsername($search);

            $html = View::getTemplate('Popups/userSearch.html', [
                'users' => $result
            ]);

            echo $html;
        }
    }
}