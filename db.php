<?php

$host = "localhost";
$username = "root";
$password = "";
$database = "student_management";

$conn = mysqli_connect(
    $host,
    $username,
    $password,
    $database
);

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8mb4");
//echo "Database connection successful!";


// ................SQL to create the database and table...........


// CREATE DATABASE student_management;

// USE student_management;
// foldar name: student_management
// foldar name: student_management/uploads


// CREATE TABLE students (
//     id INT AUTO_INCREMENT PRIMARY KEY,
//     name VARCHAR(100) NOT NULL,
//     roll INT NOT NULL,
//     `class` VARCHAR(50) NOT NULL,
//     email VARCHAR(150) NOT NULL,
//     phone VARCHAR(20) NOT NULL,
//     address VARCHAR(255) NOT NULL,
//     image VARCHAR(255) DEFAULT NULL,
//     created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
// );

?>