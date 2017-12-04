<?php

// Autoloader composer
require '../vendor/autoload.php';


//Autoloader App
spl_autoload_register(function ($class) {
    $root = dirname(__dir__); //get the parent directory
    $file = $root . '/' . str_replace('\\', '/', $class) . '.php';
    if (is_readable($file)) {
        require $root . '/' . str_replace('\\', '/', $class) . '.php';
    }
});


//Own error handler
error_reporting(E_ALL);
set_error_handler('Core\Error::errorHandler');
set_exception_handler('Core\Error::exceptionHandler');


session_start();

//must be after session init;
//if (!empty($_POST)) {
//    if (!\App\Input::checkTokenForm($_POST['token_form']))
//    {
//        throw new Exception('Token form is invalid. Probably somebody try to hack you. Please, contact admin');
//    }
//}

$router = new Core\Router();

//Routing table
$router->add('{controller}/{action}/{userid:\d+}');
$router->add('{controller}/{action}');

$router->add('', ['controller'=> 'Home', 'action'=> 'index']);
$router->add('home', ['controller'=> 'Home', 'action'=> 'index']);
$router->add('login', ['controller'=> 'Login', 'action'=> 'new']);
$router->add('signup', ['controller'=> 'Signup', 'action'=> 'new']);
$router->add('logout', ['controller'=> 'Login', 'action'=> 'destroy']);
$router->add('profile', ['controller'=> 'Profile', 'action'=> 'show']);

$router->add('password/reset/{token:[\da-f]+}', ['controller'=> 'Password', 'action'=> 'reset']);
$router->add('signup/activate/{token:[\da-f]+}', ['controller'=> 'Signup', 'action'=> 'activate']);


$router->add('admin', [
    'controller'    => 'Dashboard',
    'action'        => 'index',
    'namespace'     => 'Admin'
]);

$router->add('admin/users/index/{numpage:\d+}', [
    'controller'=> 'Users',
    'action' => 'index',
    'namespace' => 'Admin'
]);
$router->add('admin/{controller}/{action}/{userid:\d+}', [
    'namespace' => 'Admin'
]);


$router->add('admin/{controller}/{action}', [
    'namespace' => 'Admin'
]);



$router->dispatch($_SERVER['QUERY_STRING']);
