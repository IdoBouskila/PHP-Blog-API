<?php
require_once '../db/config.php';
require_once './api_functions.php';

session_start();


define('CONNECT', $connect);
header("Content-Type: application/json");

if(!isset($_SESSION['id'])) {
    json_encode([
        'status' => 401,
        'message' => 'Not allowed, you must be logged in!'
    ]);
}

if(!isset($_GET['action'])) {
    die(json_encode([
        'status' => 400,
        'message' => 'Invalid Parameters'
    ]));
}

// Execute the API based on $_GET['action] parameter
$action = $_GET['action'];

switch ($action) {
    case 'create':
        // Check if all the parameters passed correctly
        if(!is_params_valid_for_create()) {
            die(json_encode([
                'status' => 400,
                'message' => 'Invalid parameters'
            ]));
        }

        $title = $_GET['title'];
        $content = $_GET['content'];

        create_post(CONNECT, $title, $content);
        break;
    
    case 'read':
        // Check if id paramater passed
        if(!is_params_valid_read_delete()) {
            die(json_encode([
                'status' => 400,
                'message' => 'Invalid parameters'
            ]));
        }

        $post_id = $_GET['id'];
        read_post(CONNECT, $post_id);
        break;

    case 'update':
        // Check if all the parameters passed correctly
        if( !is_params_valid_for_update()) {
            die(json_encode([
                'status' => 400,
                'message' => 'Invalid parameters'
            ]));
        }

        $post_id = $_GET['id'];
        $title = $_GET['title'];
        $content = $_GET['content'];

        update_post(CONNECT, $post_id, $title, $content);
        break;

    case 'delete':
        // Check if all the parameters passed correctly
        if( !is_params_valid_read_delete()) {
            die(json_encode([
                'status' => 400,
                'message' => 'Invalid parameters'
            ]));
        }

        $post_id = $_GET['id'];

        delete_post(CONNECT, $post_id);
        break;
    
    case 'recover-post':
        // Check if all the parameters passed correctly
        if( !is_params_valid_read_delete()) {
            die(json_encode([
                'status' => 400,
                'message' => 'Invalid parameters'
            ]));
        }

        $post_id = $_GET['id'];
        recover_deleted_post(CONNECT, $post_id);
        break;

    case 'read-all-active-posts':
        read_active_posts(CONNECT);
        break;

    case 'read-user-posts':
        // Check if all the parameters passed correctly
        if( !is_params_valid_search_by_username()) {
            die(json_encode([
                'status' => 400,
                'message' => 'Invalid parameters'
            ]));
        }
        
        $username = $_GET['username'];
        read_posts_by_username($connect, $username);
        break;

    case 'read-user-active-posts':
        // Check if all the parameters passed correctly
        if( !is_params_valid_search_by_username()) {
            die(json_encode([
                'status' => 400,
                'message' => 'Invalid parameters'
            ]));
        }
        
        $username = $_GET['username'];
        read_active_posts_by_username($connect, $username);
        break;

    case 'read-user-deleted-posts':
        // Check if all the parameters passed correctly
        if( !is_params_valid_search_by_username()) {
            die(json_encode([
                'status' => 400,
                'message' => 'Invalid parameters'
            ]));
        }
        
        $username = $_GET['username'];
        read_deleted_posts_by_username($connect, $username);
        break;

    default: 
        json_encode([
            'status' => 404,
            'message' => 'Endpoint not found'
        ]);
        break;
}