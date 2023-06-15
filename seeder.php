<?php

require_once 'vendor/autoload.php';

$RecipeController = new RecipeController();
$KitchenController = new KitchenController();
$user = R::dispense('user');
$user->username = 'Daryl';
$user->password = password_hash('wachtwoord', PASSWORD_DEFAULT);
$id = R::store($user);
echo '1 user created';
