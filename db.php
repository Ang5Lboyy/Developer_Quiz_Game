<?php
$host = "localhost";
$user = "root";
$pass = "root";  // Windows-ի դեպքում դատարկ "", Mac-ում "root"
$db   = "quiz_game";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("DB connection failed: " . $conn->connect_error);
}

// Կարգավորել կոդավորումը
$conn->set_charset("utf8mb4");

// Ստուգել արդյոք users table-ը գոյություն ունի
$table_check = $conn->query("SHOW TABLES LIKE 'users'");
if($table_check->num_rows == 0) {
    $create_users = "CREATE TABLE IF NOT EXISTS `users` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `username` varchar(100) NOT NULL,
        `email` varchar(255) NOT NULL,
        `password` varchar(255) NOT NULL,
        `is_admin` tinyint(1) NOT NULL DEFAULT 0,
        `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        UNIQUE KEY `username` (`username`),
        UNIQUE KEY `email` (`email`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    $conn->query($create_users);
}
?>