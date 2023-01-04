<?php

function check_premission_and_result($connect, $post_id, $user_id) {
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

    $user_id = $_SESSION['id'];

    $status_code = 401;

    if($row['author_id'] === $user_id) {
        $status_code = 200;
    }

    return [
      'status_code' => $status_code,
      'post' => $row  
    ];
}

function post_recovery_execute($connect, $post_id, $user_id) {
    $stmt = mysqli_stmt_init($connect);
    // Soft deleting the post
    $sql = "UPDATE blog.posts
            SET deleted = FALSE, deleted_at = NULL
            WHERE id = ? AND author_id = ?;";

    if(!mysqli_stmt_prepare($stmt, $sql)) {
        die('stmt error');
    }

    mysqli_stmt_bind_param($stmt, 'ii', $post_id, $user_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}

function soft_delete_execute($connect, $post_id) {
    $stmt = mysqli_stmt_init($connect);
    $sql = "UPDATE blog.posts
            SET deleted = TRUE, deleted_at = CURRENT_TIMESTAMP
            WHERE id = ? AND author_id = ?;";

    if(!mysqli_stmt_prepare($stmt, $sql)) {
        die('stmt error');
    }

    mysqli_stmt_bind_param($stmt, 'ii', $post_id, $user_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}

function update_post_execute($connect, $post_id, $title, $content, $user_id) {
    $stmt = mysqli_stmt_init($connect);
    $sql = "UPDATE blog.posts
            SET title = ?, content = ?
            WHERE id = ? AND author_id = ?;";

    if(!mysqli_stmt_prepare($stmt, $sql)) {
        die('stmt error');
    }

    mysqli_stmt_bind_param($stmt, 'ssii', $title, $content, $post_id, $user_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}

function create_post_execute($connect, $title, $content) {
    $stmt = mysqli_stmt_init($connect);
    $sql = "INSERT INTO blog.posts
            (`author_id`, `title`, `content`) 
            VALUES 
            (?, ?, ?);";
    
    if(!mysqli_stmt_prepare($stmt, $sql)) {
        die('stmt error');
    }
    
    $user_id = $_SESSION['id'];

    mysqli_stmt_bind_param($stmt, 'iss', $user_id, $title, $content);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    // Reading the post that created
    $result = mysqli_query($connect, "SELECT * FROM blog.posts WHERE id = LAST_INSERT_ID();");

    $row = mysqli_fetch_assoc($result);
    return $row;
}
