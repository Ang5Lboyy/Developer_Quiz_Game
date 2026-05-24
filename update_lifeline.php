<?php
session_start();
include "db.php";

if(!isset($_SESSION["user_id"])) {
    exit;
}

if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["type"])) {
    $type = $_POST["type"];
    $user_id = $_SESSION["user_id"];
    
    if($type == "fifty_fifty") {
        $conn->query("UPDATE users SET fifty_fifty_available = 0 WHERE id = $user_id");
    } elseif($type == "audience") {
        $conn->query("UPDATE users SET audience_available = 0 WHERE id = $user_id");
    }
}
?>