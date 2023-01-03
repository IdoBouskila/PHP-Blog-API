<?php
$servername = 'localhost';
$db_username = 'root';
$db_password = '';
$db_name = 'blog';

// Connect to the database
$connect = mysqli_connect($servername, $db_username, $db_password);

if (!$connect) {
  die('Connection failed: ' . mysqli_connect_error());
}