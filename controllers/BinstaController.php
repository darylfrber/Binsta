<?php

use RedBeanPHP\R as R;

class BinstaController extends BaseController
{
    final public function feed(): void // Toon alle posts
    {
        $this->authorizedUser(); // Kijk of gebruiker is ingelogd
        $comments_posts = R::findAll('comments', ' ORDER BY id ASC ');
        $liked_posts = R::find('likes', ' WHERE user_id = ? ', [$_SESSION['user_id']]);
        $data['posts'] = $this->getAllBeans('posts');
        $data['comments'] = $comments_posts;
        $data['likes'] = $liked_posts;
        render('/binsta/feed.twig', $data);
        exit();
    }

    final public function post(): void // Pagina om een nieuwe post aan te maken voor de feed.
    {
        $this->authorizedUser(); // Kijk of gebruiker is ingelogd
        $data = [];
        render('/binsta/post.twig', $data);
        exit();
    }

    final public function postPost($description, $code, $language): void // Post de post...
    {
        $posts = R::dispense('posts'); // Maak een nieuwe gebruiker in table user
        $posts->description = $description;
        $posts->code = $code;
        $posts->language = $language;
        $posts->likes = 0;
        $posts->user_id = $_SESSION['user_id'];
        R::store($posts);
        header("location: feed");
        exit();
    }

    final public function profile($user): void // Toon profiel van een gebruiker
    {
        $info = $this->getInfo('user', 'username', $user);
        $user_posts = R::find('posts', ' user_id = ? ORDER BY id DESC ', [$info['id']]);
        $comments_posts = R::findAll('comments', ' ORDER BY id ASC ');
        $liked_posts = R::find('likes', ' WHERE user_id = ? ', [$_SESSION['user_id']]);
        if (count($user_posts) == 0) {
            $user_posts = 'Deze gebruiker heeft nog geen posts geplaatst';
        }
        $data['profile'] = $info;
        $data['posts'] = $user_posts;
        $data['comments'] = $comments_posts;
        $data['likes'] = $liked_posts;
        render('/binsta/profile.twig', $data);
        exit();
    }

    final public function settings(): void // Instellingen van gebruiker
    {
        $data = [];
        render('/binsta/settings.twig', $data);
        exit();
    }

    final public function settingsProfilePost($username, $biography): void // Post de post...
    {
        $user = R::load('user', $_SESSION['user_id']);
        $user->username = $username;
        $user->biography = $biography;
        R::store($user);
        header("location: /profile/$username");
        exit();
    }

    final public function settingsPasswordPost($current_password, $new_password, $confirm_password): void // Post de post...
    {
        $user = R::load('user', $_SESSION['user_id']);

        if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
            $_SESSION['error'] = "Fill in all fields";
            return;
        }
        if (!password_verify($current_password, $user['password'])) {
            $_SESSION['error'] = "Current password is incorrect";
            return;
        }

        if ($new_password != $confirm_password) {
            $_SESSION['error'] = "Passwords don't match";
            return;
        }

        $user->password = password_hash($new_password, PASSWORD_DEFAULT);
        R::store($user);
        header("location: /logout");
        exit();
    }

    final public function settingsPicturePost($image): void // Post de post...
    {
        if ($image['size'] == 0) { // Als er geen bestand word geupload 
            $_SESSION['error'] = 'No image was selected';
            return;
        }
        $image_name = pathinfo($image["name"])['extension']; // Bekijk de extensie naam
        move_uploaded_file($image["tmp_name"], dirname(__DIR__, 1) . "/images/" . $_SESSION['user_id'] . "_" . date('his') . "." . $image_name);

        // Verwijs naar de image in de database
        $user = R::load('user', $_SESSION['user_id']);
        $user->image = "/images/" . $_SESSION['user_id'] . "_" . date('his') . "." . $image_name;
        R::store($user);
    }

    final public function likePost($post_id): void // Post de post...
    {
        $likes = R::dispense('likes'); // Maak een nieuwe like aan
        $likes->post_id = $post_id;
        $likes->user_id = $_SESSION['user_id'];
        R::store($likes);

        $post = R::load('posts', $post_id); // Update aantal likes
        $post->likes++;
        R::store($post);
    }

    final public function unlikePost($post_id, $like_id): void // Post de post...
    {
        $likes = R::load('likes', $like_id); // Maak een nieuwe like aan
        R::trash($likes);

        $post = R::load('posts', $post_id); // Update aantal likes
        $post->likes--;
        R::store($post);
    }

    final public function commentPost($post_id, $comment): void // Post de post...
    {
        if (empty($comment)) {
            $_SESSION['error'] = 'You can\'t place empty comments';
            return;
        }
        $comments = R::dispense('comments'); // Maak een comment aan
        $comments->post_id = $post_id;
        $comments->comment = $comment;
        $comments->user_id = $_SESSION['user_id'];
        R::store($comments);
    }
}
