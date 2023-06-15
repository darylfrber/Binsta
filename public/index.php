<?php

require_once '../vendor/autoload.php';
$UserController = new UserController();
$BinstaController = new BinstaController();

if (isset($_POST['search_profile'])) { // Gebruiker zoekbalk in navigatie
    header('Location: /profile/' . $_POST['search_name']); // Stuur door naar profiel, als profiel niet bestaat krijg je error pagina
    exit();
}

if (isset($_POST['like_post'])) { // Gebruiker liked een post
    $BinstaController->likePost($_POST['like_post_id']);
}

if (isset($_POST['unlike_post'])) { // Gebruiker liked een post
    $BinstaController->unlikePost($_POST['unlike_post_id'], $_POST['unlike_like_id']);
}


if (isset($_POST['comment_post'])) { // Gebruiker plaatst comment
    $BinstaController->commentPost($_POST['comment_post_id'], $_POST['comment_comment']);
}

if (isset($_GET['controller'])) { // Kijken of de controller gezet is
    if ($_GET['controller'] == 'User' && isset($_GET['method'])) { // Als de controller user is en er is een methode opgeroepen
        if ($_GET['method'] == "login") { // Als de methode login is
            if (isset($_POST['user_login'])) { // Als een gebruiker een login poging doet
                $UserController->loginPost($_POST['user_username'], $_POST['user_password']); // Controlleer de poging
            }
            $UserController->login(); // Geef de login pagina
        }
        if ($_GET['method'] == "register") { // Als method register is 
            if (isset($_POST['user_register'])) { // Wanneer gebruiker registratie probeert
                $UserController->registerPost($_POST['new_user_username'], $_POST['new_user_password'], $_POST['new_user_password_repeat']);
            }
            $UserController->register(); // Geef registratie pagina
        }
        error('404', 'Method \'' . $_GET['method'] . '\' not found');
    } else if ($_GET['controller'] == 'Binsta' && isset($_GET['method'])) {
        if ($_GET['method'] == "feed") {
            $BinstaController->feed(); // Show feed
        }
        if ($_GET['method'] == "post") {
            if (isset($_POST['post_submit'])) { // Gebruiker heeft submit gedrukt
                $BinstaController->postPost($_POST['post_description'], $_POST['post_code'], $_POST['post_language']);
            }
            $BinstaController->post(); // Create a post for the feed
        }
        if ($_GET['method'] == "profile") {
            $user_profile = basename($_SERVER['REQUEST_URI']);
            $BinstaController->profile($user_profile); // Show profile of given user
        }
        if ($_GET['method'] == "settings") {
            if (isset($_POST['settings_profile'])) { // Gebruiker heeft submit gedrukt
                $BinstaController->settingsProfilePost($_POST['user_username'], $_POST['user_biography']);
            }
            if (isset($_POST['settings_picture'])) { // Gebruiker heeft submit gedrukt
                $BinstaController->settingsPicturePost($_FILES["user_picture"]);
            }
            if (isset($_POST['settings_password'])) { // Gebruiker heeft submit gedrukt
                $BinstaController->settingsPasswordPost($_POST['user_current_password'], $_POST['user_new_password'], $_POST['user_confirm_new_password']);
            }
            $BinstaController->settings(); // Show profile of given user
        }
        error('404', 'Method \'' . $_GET['method'] . '\' not found');
    } else {
        error('404', 'Controller \'' . $_GET['controller'] . 'Controller\' not found');
    }
} else {
    $_GET['controller'] = ucfirst(basename($_SERVER['REQUEST_URI']));
    error('404', 'Controller \'' . $_GET['controller'] . 'Controller\' not found');
}
