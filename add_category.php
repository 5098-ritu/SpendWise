<?php
session_start();
include("config.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = mysqli_real_escape_string($conn, $_POST['name']);

    $insert = "INSERT INTO categories (user_id, name) 
               VALUES ($user_id, '$name')";

    if ($conn->query($insert)) {
        header("Location: categories.php");
        exit();
    } else {
        echo "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Category</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="add.css">
</head>
<body>

<div class="add-container">
    <div class="add-card">
        <h2>Add Category</h2>
        <p class="subtitle">Create a new expense category</p>

        <form method="POST">
            <label>Category Name</label>
            <input type="text" name="name" required>

            <div class="button-group">
                <button type="submit" class="btn-primary">Add Category</button>
                <a href="categories.php" class="btn-cancel">Cancel</a>
            </div>
        </form>
    </div>
</div>

</body>
</html>