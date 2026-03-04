<?php
$servername = "localhost";
$username = "root";
$password = "mango";
$dbname = "fintrack";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>