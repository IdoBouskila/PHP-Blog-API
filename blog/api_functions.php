<?php
require_once './includes.php';
require_once '../db/db.php';

function create_post($connect, $title, $content) {
    $result = create_post_execute($connect, $title, $content);
    
    die(json_encode([
        'status' => 200,
        'post' => $result
    ]));
    
}

function read_post($connect, $post_id) {
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
        die(json_encode([
            'status' => 404,
            'message' => 'Post not found!'
        ]));
    }
    
    die(json_encode([
        'status' => 200,
        'post' => $row
    ]));
}

function update_post($connect, $post_id, $title, $content) {
    if(!is_postid_valid($connect, $post_id)) {
        die(json_encode([
            'status' => 404,
            'message' => 'Invalid post ID'
        ]));
    }

    $user_id = $_SESSION['id'];

    // First query - updating the post
    update_post_execute($connect, $post_id, $title, $content, $user_id);

    // Second query - Check premission and return the post
    $data = check_premission_and_result($connect, $post_id, $user_id);
    $status_code = $data['status_code'];
    $post = $data['post'];


    die(json_encode([
        'status' => $status_code,
        'post' => $post
    ]));
}

function delete_post($connect, $post_id) {
    if(!is_postid_valid($connect, $post_id)) {
        die(json_encode([
            'status' => 404,
            'message' => 'Invalid post ID'
        ]));
    }

    $user_id = $_SESSION['id'];

    soft_delete_execute($connect, $post_id, $user_id);

    // Second query - Check premission and return the post
    $data = check_premission_and_result($connect, $post_id, $user_id);
    $status_code = $data['status_code'];
    $post = $data['post'];


    die(json_encode([
        'status' => $status_code,
        'post' => $post
    ]));
}

function recover_deleted_post($connect, $post_id) {
    if(!is_postid_valid($connect, $post_id)) {
        die(json_encode([
            'status' => 404,
            'message' => 'Invalid post ID'
        ]));
    }

    $user_id = $_SESSION['id'];

    post_recovery_execute($connect, $post_id, $user_id);

    // Second query - reading the post that recently updated
    $data = check_premission_and_result($connect, $post_id, $user_id);
    $status_code = $data['status_code'];
    $post = $data['post'];


    die(json_encode([
        'status' => $status_code,
        'post' => $post
    ]));
}

function read_active_posts($connect) {
    $stmt = mysqli_stmt_init($connect);
    $sql = "SELECT * FROM blog.posts
            WHERE deleted != 1;";

    if(!mysqli_stmt_prepare($stmt, $sql)) {
        die('stmt error');
    }

    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    mysqli_stmt_close($stmt);
    
    $data = [];
    while($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
    
    die(json_encode([
        'status' => 200,
        'posts' => $data
    ]));
}

function read_posts_by_username($connect, $username) {
    if(!is_username_exists($connect, $username)) {
        die(json_encode([
            'status' => 200,
            'Username not found'
        ]));
    }

    $stmt = mysqli_stmt_init($connect);
    $sql = "SELECT blog.users.username, blog.posts.*
            FROM blog.users
            JOIN blog.posts on blog.posts.author_id = blog.users.id
            WHERE username = ?;";

    if(!mysqli_stmt_prepare($stmt, $sql)) {
        die('stmt error');
    }
    mysqli_stmt_bind_param($stmt, 's', $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    mysqli_stmt_close($stmt);
    
    $data = [];
    while($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }

    die(json_encode([
        'status' => 200,
        'posts' => $data
    ]));
}

function read_active_posts_by_username($connect, $username) {
    if(!is_username_exists($connect, $username)) {
        die(json_encode([
            'status' => 200,
            'Username not found'
        ]));
    }

    $stmt = mysqli_stmt_init($connect);
    $sql = "SELECT blog.users.username, blog.posts.*
            FROM blog.users
            JOIN blog.posts on blog.posts.author_id = blog.users.id
            WHERE username = ? AND deleted = 0;";

    if(!mysqli_stmt_prepare($stmt, $sql)) {
        die('stmt error');
    }
    mysqli_stmt_bind_param($stmt, 's', $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    mysqli_stmt_close($stmt);


    
    $data = [];
    while($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
    
    die(json_encode([
        'status' => 200,
        'posts' => $data
    ]));
}

function read_deleted_posts_by_username($connect, $username) {
    if(!is_username_exists($connect, $username)) {
        die(json_encode([
            'status' => 200,
            'Username not found'
        ]));
    }
    
    $stmt = mysqli_stmt_init($connect);
    $sql = "SELECT blog.users.username, blog.posts.*
            FROM blog.users
            JOIN blog.posts on blog.posts.author_id = blog.users.id
            WHERE username = ? AND deleted = 1;";

    if(!mysqli_stmt_prepare($stmt, $sql)) {
        die('stmt error');
    }
    mysqli_stmt_bind_param($stmt, 's', $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    mysqli_stmt_close($stmt);


    
    $data = [];
    while($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
    
    die(json_encode([
        'status' => 200,
        'posts' => $data
    ]));
}
