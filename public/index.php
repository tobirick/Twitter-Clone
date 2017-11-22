<?php
/**
 * Created by PhpStorm.
 * User: tobirick
 * Date: 13.11.17
 * Time: 19:08
 */

//ini_set('session.cookie_lifetime', '864000');

// Composer autoloader
require_once dirname(__DIR__) . '/vendor/autoload.php';

// Error and Exception Handler
error_reporting(E_ALL);
set_error_handler('Core\Error::errorHandler');
set_exception_handler('Core\Error::exceptionHandler');

// Sessions
session_start();

// Routing
$router = new Core\Router();

// user url
$router->add('users/{user:[\da-z]+}', ['controller' => 'Users', 'action' => 'index']);
$router->add('', ['controller' => 'Home', 'action' => 'index']);
$router->add('logout', ['controller' => 'Login', 'action' => 'destroy']);
$router->add('{controller}');
$router->add('{controller}/{action}');
//$router->add('{controller}/{id:\d+}/{action}');
// Password reset url
$router->add('password/reset/{token:[\da-f]+}', ['controller' => 'Password', 'action' => 'reset']);
// activation url
$router->add('signup/activate/{token:[\da-f]+}', ['controller' => 'Signup', 'action' => 'activate']);
// Admin Pages
$router->add('admin/{controller}', ['namespace' => 'Admin']);
$router->add('admin/{controller}/{action}', ['namespace' => 'Admin']);

$router->dispatch($_SERVER['QUERY_STRING']);

