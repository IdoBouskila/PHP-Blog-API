<?php
require_once './db/config.php';
require_once './auth/functions.php';

session_start();

define('CONNECT', $connect);

function sign_up() {
    $email = $_POST['email'];
    $username = $_POST['username'];
    $plaintext_password = $_POST['password'];

    // Validations
    if(is_sign_up_fields_empty($email, $username, $plaintext_password)) {
        header('location: ./sign-up.php?error=Fields cannot be empty!');
        die();
    }

    if(is_user_exists(CONNECT, $email, $username)) {
        header('location: ./sign-up.php?error=Email or Username already in used!');
        die();
    }

    register_to_db(CONNECT, $email, $username, $plaintext_password);
}

function sign_in() {
    $username = $_POST['username'];
    $plaintext_password = $_POST['password'];

    if(!is_user_verify(CONNECT, $username, $plaintext_password)) {
        header('location: ./login.php?error=Username or Password is incorrect');
        die();
    }

    // remember the username login by saving the username on SESSION and redirect to Index.php
    $_SESSION['user'] = $username;

    header('location: ./index.php');
    die();
}