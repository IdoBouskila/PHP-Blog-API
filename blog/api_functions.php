<?php
require_once './includes.php';

function create_post($connect, $user_id, $title, $content) {
    if(!is_userid_valid($connect, $user_id)) {
        die(json_encode([
            'status' => 404,
            'message' => 'Invalid UserID'
        ]));
    }

    $stmt = mysqli_stmt_init($connect);
    $sql = "INSERT INTO blog.posts
            (`author_id`, `title`, `content`) 
            VALUES 
            (?, ?, ?);";
            
    if(!mysqli_stmt_prepare($stmt, $sql)) {
        die('stmt error');
    }

    mysqli_stmt_bind_param($stmt, 'iss', $user_id, $title, $content);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    // Reading the post that created
    $result = mysqli_query($connect, "SELECT * FROM blog.posts WHERE id = LAST_INSERT_ID();");

    $row = mysqli_fetch_assoc($result);
    
    die(json_encode([
        'status' => 200,
        'post' => $row
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

    // First query - updating the post
    $stmt = mysqli_stmt_init($connect);
    $sql = "UPDATE blog.posts
            SET title = ?, content = ?
            WHERE id = ?;";

    if(!mysqli_stmt_prepare($stmt, $sql)) {
        die('stmt error');
    }

    mysqli_stmt_bind_param($stmt, 'ssi', $title, $content, $post_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    // Second query - reading the post that recently updated
    $stmt = mysqli_stmt_init($connect);
    $sql = "SELECT * FROM blog.posts
            WHERE id = ?";

    if(!mysqli_stmt_prepare($stmt, $sql)) {
        die('stmt error');
    }

    mysqli_stmt_bind_param($stmt, 'i', $post_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    mysqli_stmt_close($stmt);

    $row = mysqli_fetch_assoc($result);
    
    die(json_encode([
        'status' => 200,
        'post' => $row
    ]));
}

function delete_post($connect, $post_id) {
    if(!is_postid_valid($connect, $post_id)) {
        die(json_encode([
            'status' => 404,
            'message' => 'Invalid post ID'
        ]));
    }

    $stmt = mysqli_stmt_init($connect);
    // Soft deleting the post
    $sql = "UPDATE blog.posts
            SET deleted = TRUE, deleted_at = CURRENT_TIMESTAMP
            WHERE id = ?;";

    if(!mysqli_stmt_prepare($stmt, $sql)) {
        die('stmt error');
    }

    mysqli_stmt_bind_param($stmt, 'i', $post_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    // Second query - reading the post that recently updated
    $stmt = mysqli_stmt_init($connect);
    $sql = "SELECT * FROM blog.posts
            WHERE id = ?";

    if(!mysqli_stmt_prepare($stmt, $sql)) {
        die('stmt error');
    }
    
    mysqli_stmt_bind_param($stmt, 'i', $post_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    mysqli_stmt_close($stmt);

    $row = mysqli_fetch_assoc($result);
    
    die(json_encode([
        'status' => 200,
        'post' => $row
    ]));
}

function recover_deleted_post($connect, $post_id) {
    if(!is_postid_valid($connect, $post_id)) {
        die(json_encode([
            'status' => 404,
            'message' => 'Invalid post ID'
        ]));
    }

    $stmt = mysqli_stmt_init($connect);
    // Soft deleting the post
    $sql = "UPDATE blog.posts
            SET deleted = FALSE, deleted_at = NULL
            WHERE id = ?;";

    if(!mysqli_stmt_prepare($stmt, $sql)) {
        die('stmt error');
    }

    mysqli_stmt_bind_param($stmt, 'i', $post_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    // Second query - reading the post that recently updated
    $stmt = mysqli_stmt_init($connect);
    $sql = "SELECT * FROM blog.posts
            WHERE id = ?";

    if(!mysqli_stmt_prepare($stmt, $sql)) {
        die('stmt error');
    }
    
    mysqli_stmt_bind_param($stmt, 'i', $post_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    mysqli_stmt_close($stmt);

    $row = mysqli_fetch_assoc($result);
    
    die(json_encode([
        'status' => 200,
        'post' => $row
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
