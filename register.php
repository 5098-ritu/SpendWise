<?php
session_start();
include("config.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Encrypt password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert user into database
    $sql = "INSERT INTO users (username, email, password) 
            VALUES ('$username', '$email', '$hashed_password')";

    if ($conn->query($sql) === TRUE) {

        // After successful registration redirect to login
        header("Location: login.html");
        exit();

    } else {
        echo "Error: " . $conn->error;
    }

    $conn->close();
}
?>