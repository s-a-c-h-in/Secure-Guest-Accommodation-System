<?php
// db.php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "guest_accommodation";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

// sql Database Creation

CREATE DATABASE guest_accommodation;

USE guest_accommodation;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL
);

/// Table Databse Creation (Create a new table in the ("guest_accommodation") database called login_attempts.)

CREATE TABLE login_attempts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL,
    attempts INT DEFAULT 0,
    last_attempt TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

** This creates a table to store:
1. username
2. how many wrong tries
3. last attempt time
