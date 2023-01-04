<?php

function redirect_logged_in_user() {
    if(isset($_SESSION['user'])) {
        header('location: ./index.php');
    }
}

function is_sign_up_fields_empty($email, $username, $plaintext_password) {
    if(!strlen($email) || !strlen($username) || !strlen($plaintext_password)) {
        return true;
    }

    return false;
}

function is_user_exists($connect, $email, $username) {
    $stmt = mysqli_stmt_init($connect);
    $sql = "SELECT * FROM blog.users WHERE email = ? OR username = ?;";
    
    if(!mysqli_stmt_prepare($stmt, $sql)) {
        header('location: ./sign-up.php?error=stmtfail');
        die();
    }

    mysqli_stmt_bind_param($stmt, 'ss', $email, $username);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    if(mysqli_fetch_assoc($result)) {
        return true;
    }

    return false;

    mysqli_stmt_close($stmt);
}


function register_to_db($connect, $email, $username, $plaintext_password) {
    $stmt = mysqli_stmt_init($connect);
    $sql = "INSERT INTO blog.users (email, username, password) VALUES (?, ?, ?);";
    
    if(!mysqli_stmt_prepare($stmt, $sql)) {
        header('location: ./sign-up.php?error=stmtfail');
        die();
    }

    // register the user and redirect to index page
    mysqli_stmt_bind_param($stmt, 'sss', $email, $username, password_hash($plaintext_password, PASSWORD_DEFAULT));
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    header('location: ./index.php');
    die();


}

function is_sign_in_fields_empty($username, $password) {
    if(!strlen($username) || !strlen($password)) {
        return true;
    }

    return false;
}

function is_user_verify($connect, $username, $plaintext_password) {
    $stmt = mysqli_stmt_init(($connect));
    $sql = "SELECT * FROM blog.users WHERE username = ?";
    
    if(!mysqli_stmt_prepare($stmt, $sql)) {
        header('location: ./sign-up.php?error=stmtfail');
        die();
    }

    mysqli_stmt_bind_param($stmt, 's', $username);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    mysqli_stmt_close($stmt);

    if(!$row = mysqli_fetch_assoc($result)) {
        return false;
    }
    
    $hashed_password = $row['password'];
    if(!password_verify($plaintext_password, $hashed_password)) {
        return false;
    }

    // remember the username login by saving the id on SESSION

    $_SESSION['id'] = $row['id'];
    return true;
}