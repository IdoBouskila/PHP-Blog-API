
<?php
require_once '../db/config.php';
define('CONNECT', $connect);

function create_post($connect, $user_id, $title, $content) {
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


    header("Content-Type: application/json");

    // Reading the post that created
    $resultData = mysqli_query($connect, "SELECT * FROM blog.posts WHERE id = LAST_INSERT_ID();");

    if(!$row = mysqli_fetch_assoc($resultData)) {
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

function read_post($connect, $post_id) {
    $stmt = mysqli_stmt_init($connect);
    $sql = "SELECT * FROM blog.posts WHERE id = ?;";

    if(!mysqli_stmt_prepare($stmt, $sql)) {
        die('stmt error');
    }

    mysqli_stmt_bind_param($stmt, 'i', $post_id);
    mysqli_stmt_execute($stmt);
    $resultData = mysqli_stmt_get_result($stmt);
    mysqli_stmt_close($stmt);

    header("Content-Type: application/json");

    if(!$row = mysqli_fetch_assoc($resultData)) {
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
    $resultData = mysqli_stmt_get_result($stmt);
    mysqli_stmt_close($stmt);

    header("Content-Type: application/json"); 

    if(!$row = mysqli_fetch_assoc($resultData)) {
        
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

function delete_post($connect, $post_id) {
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
    $resultData = mysqli_stmt_get_result($stmt);
    mysqli_stmt_close($stmt);

    header("Content-Type: application/json"); 

    if(!$row = mysqli_fetch_assoc($resultData)) {
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

function recover_deleted_post($connect, $post_id) {
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
    $resultData = mysqli_stmt_get_result($stmt);
    mysqli_stmt_close($stmt);

    header("Content-Type: application/json"); 

    if(!$row = mysqli_fetch_assoc($resultData)) {
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

function read_active_posts($connect) {
    $stmt = mysqli_stmt_init($connect);
    $sql = "SELECT * FROM blog.posts
    WHERE deleted != 1;";

    if(!mysqli_stmt_prepare($stmt, $sql)) {
        die('stmt error');
    }

    mysqli_stmt_execute($stmt);
    $resultData = mysqli_stmt_get_result($stmt);
    mysqli_stmt_close($stmt);


    
    $data = [];
    while($row = mysqli_fetch_assoc($resultData)) {
        $data[] = $row;
    }
    
    header("Content-Type: application/json"); 

    die(json_encode([
        'status' => 200,
        'posts' => $data
    ]));
}

function get_posts_by_username($connect, $username) {
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
    $resultData = mysqli_stmt_get_result($stmt);
    mysqli_stmt_close($stmt);


    
    $data = [];
    while($row = mysqli_fetch_assoc($resultData)) {
        $data[] = $row;
    }
    
    header("Content-Type: application/json"); 

    die(json_encode([
        'status' => 200,
        'posts' => $data
    ]));
}

function get_active_posts_by_username($connect, $username) {
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
    $resultData = mysqli_stmt_get_result($stmt);
    mysqli_stmt_close($stmt);


    
    $data = [];
    while($row = mysqli_fetch_assoc($resultData)) {
        $data[] = $row;
    }
    
    header("Content-Type: application/json"); 

    die(json_encode([
        'status' => 200,
        'posts' => $data
    ]));
}

function get_deleted_posts_by_username($connect, $username) {
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
    $resultData = mysqli_stmt_get_result($stmt);
    mysqli_stmt_close($stmt);


    
    $data = [];
    while($row = mysqli_fetch_assoc($resultData)) {
        $data[] = $row;
    }
    
    header("Content-Type: application/json"); 

    die(json_encode([
        'status' => 200,
        'posts' => $data
    ]));
}
