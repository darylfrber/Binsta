<?php

$host = '127.0.0.1';
$db   = 'binsta';
$user = 'root';
$pass = 'DarylSQL1232020!';

$dsn = "mysql:host=$host;dbname=$db";
use RedBeanPHP\R as R;
R::setup(
    $dsn,
    $user,
    $pass
);
session_start();
require_once 'controllers/BaseController.php';
require_once 'controllers/UserController.php';
require_once 'controllers/BinstaController.php';
function render($template, $data)
{
    $loader = new \Twig\Loader\FilesystemLoader('../views');
    $twig = new \Twig\Environment($loader);
    if (isset($_SESSION['error'])) {
        $data['error'] = $_SESSION['error'];
    }
        
    if (isset($_SESSION['user_id'])) { // Als een gebruiker is ingelogd, geef de template user info
        $beans = R::find('user', ' id = ? ', [$_SESSION['user_id']]);
        foreach ($beans as $bean) {
            $data['user'] = $bean;
        }
    }
    echo $twig->render($template, $data);
    if (isset($_SESSION['error'])) {
        unset($_SESSION['error']);
    }
}

function error($errorNumber, $errorMessage)
{
    $data['errorNumber'] = $errorNumber;
    $data['errorMessage'] = $errorMessage;
    http_response_code($errorNumber);
    render('/error.twig', $data);
    exit();
}