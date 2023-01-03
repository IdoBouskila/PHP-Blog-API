
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

    if(!$row = mysqli_fetch_assoc($resultData)) {
        header("Content-Type: application/json"); 

        die(json_encode([
            'status' => 404,
            'message' => 'Post not found!'
        ]));
    }
    
    header("Content-Type: application/json"); 

    echo json_encode([
        'status' => 200,
        'post' => $row
    ]);
}

function update_post($connect, $post_id) {
    
}

// TODO: Add Deleted True and Deleted timestamp
function delete_post($connect, $post_id) {
    $stmt = mysqli_stmt_init($connect);
    // Soft deleting the post
    $sql = "DELETE FROM blog.posts WHERE id = ?;";

    if(!mysqli_stmt_prepare($stmt, $sql)) {
        die('stmt error');
    }

    mysqli_stmt_bind_param($stmt, 'i', $post_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}
