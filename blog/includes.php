<?php



function is_params_valid_create_update() {
    if( ! (array_key_exists('id', $_GET) && array_key_exists('title', $_GET) && array_key_exists('content', $_GET)) ) {
        return false;
    }

    return true;
}

function is_params_valid_read_delete() {
    if( !array_key_exists('id', $_GET)) {
        return false;
    }

    return true;
}

function is_params_valid_search_by_username() {
    if( !array_key_exists('username', $_GET)) {
        return false;
    }

    return true;
}

function is_username_exists($connect, $username) {
    $stmt = mysqli_stmt_init($connect);
    $sql = "SELECT * FROM blog.users WHERE username = ?;";
    
    if(!mysqli_stmt_prepare($stmt, $sql)) {
        die('stmt error');
    }
    
    mysqli_stmt_bind_param($stmt, 's', $username);
    mysqli_stmt_execute($stmt);
    
    $result = mysqli_stmt_get_result($stmt);
    mysqli_stmt_close($stmt);
    
    if(!$row = mysqli_fetch_assoc($result)) {
        return false;
    }
    
    return true;
}

function is_userid_valid($connect, $user_id) {
    $stmt = mysqli_stmt_init($connect);
    $sql = "SELECT * FROM blog.posts WHERE author_id = ?;";
    
    if(!mysqli_stmt_prepare($stmt, $sql)) {
        die('stmt error');
    }
    
    mysqli_stmt_bind_param($stmt, 'i', $user_id);
    mysqli_stmt_execute($stmt);
    
    $result = mysqli_stmt_get_result($stmt);
    mysqli_stmt_close($stmt);
    
    if(!$row = mysqli_fetch_assoc($result)) {
        return false;
    }
    
    return true;
}

function is_postid_valid($connect, $post_id) {
    $stmt = mysqli_stmt_init($connect);
    $sql = "SELECT * FROM blog.posts WHERE id = ?;";
    
    if(!mysqli_stmt_prepare($stmt, $sql)) {
        die('stmt error');
    }

    mysqli_stmt_bind_param($stmt, 'i', $post_id);
    mysqli_stmt_execute($stmt);
    
    $result = mysqli_stmt_get_result($stmt);
    mysqli_stmt_close($stmt);
        
    if(!$row = mysqli_fetch_assoc($result)) {
        return false;
    }

    return true;
}
