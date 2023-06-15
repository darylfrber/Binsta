<?php

use RedBeanPHP\R as R;

class UserController extends BaseController
{
    public function login(): void // Toon login pagina
    {
        $this->alreadyLoggedIn(); // Als gebruiker al ingelogd is, stuur hem terug naar feed page.
        $data = [];
        render('/user/login.twig', $data);
        exit();
    }

    public function loginPost($username, $password): void // Bekijk of de ingegeven gegevens overeenkomen met die van de database
    {
        $user_details = R::find('user', ' username = ?', [$username]);
        if (count($user_details) == 1) { // Kijken of er een gebruiker is gevonden
            foreach ($user_details as $user) {
                if (password_verify($password, $user['password'])) {
                    $_SESSION['user_id'] = $user['id'];
                    header("Location: feed");
                } else {
                    $_SESSION['error'] = "Password is incorrect";
                    return;
                }
            }
        } else {
            $_SESSION['error'] = "No user found";
            return;
        }
    }

    public function register(): void // Toon login pagina
    {
        $this->alreadyLoggedIn(); // Als gebruiker al ingelogd is, stuur hem terug naar feed page.
        $data = [];
        render('/user/register.twig', $data); // Laad de pagina
        exit();
    }

    public function registerPost($username, $password, $confirm_password): void // Bekijk of de ingegeven gegevens overeenkomen met die van de database
    {
        if (empty($username) || empty($password) || empty($confirm_password)) {
            $_SESSION['error'] = "Vul alle velden in";
            return;
        }
        $user_details = R::find('user', ' username = ?', [$username]); // ? is username
        if (count($user_details) > 0) { // Kijk of deze gebruikersnaam al bestaat
            $_SESSION['error'] = "Deze gebruikersnaam bestaat al";
            return;
        }
        if ($password != $confirm_password) { // Controleer of herhaalde wachtwoord zelfde is als wachtwoord
            $_SESSION['error'] = "Wachtwoorden komen niet overeen";
            return;
        }
        $register = R::dispense('user'); // Maak een nieuwe gebruiker in table user
        $register->username = $username;
        $register->password = password_hash($password, PASSWORD_DEFAULT);
        $register->image = "/images/user.png";
        $id = R::store($register);
        $_SESSION['user_id'] = $id;
        header("location: feed");
        exit();
    }
}
