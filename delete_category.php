<?php
session_start();
include("config.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

if (isset($_GET['id'])) {

    $id = intval($_GET['id']);
    $user_id = $_SESSION['user_id'];

    $conn->query("DELETE FROM categories WHERE id=$id AND user_id=$user_id");
}

header("Location: categories.php");
exit();
?>