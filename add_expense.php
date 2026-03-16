<?php
session_start();
include("config.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $user_id = $_SESSION['user_id'];
    $title = $_POST['title'];
    $amount = $_POST['amount'];
    $category = $_POST['category'];
    $expense_date = $_POST['expense_date'];

    $sql = "INSERT INTO expenses (user_id, title, amount, category, type, expense_date)
            VALUES ('$user_id', '$title', '$amount', '$category', 'expense', '$expense_date')";

    $conn->query($sql);

    header("Location: dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Expense - SpendWise</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="add_expense.css">
</head>
<body>

<header class="navbar">
    <div class="logo">
        <div class="logo-icon">
            <span class="big-f">S</span>
            <span class="small-t">w</span>
        </div>
        SpendWise
    </div>

    <nav>
        <a href="dashboard.php">Dashboard</a>
        <a href="add_expense.php" class="active">Add Expense</a>
        <a href="expenses.php" class="active">Expenses</a>
          <a href="categories.php">Categories</a>
        <a href="analytics.php">Analytics</a>
        <a href="logout.php" class="btn-outline">Logout</a>
    </nav>
</header>

<section class="add-container">

    <div class="add-card">
        <h2>Add New Expense</h2>
        <p class="subtitle">Track your spending by adding a new expense</p>

        <form method="POST">

            <label>Expense Title *</label>
            <input type="text" name="title" placeholder="e.g., Grocery Shopping" required>

            <label>Amount *</label>
            <input type="number" step="0.01" name="amount" placeholder="0.00" required>

            <label>Category *</label>
            <select name="category" required>
                <option value="">Select a category</option>
                <option>Food</option>
                <option>Transportation</option>
                <option>Education</option>
                <option>Bills</option>
                <option>Entertainment</option>
                <option>Shopping</option>
            </select>

            <label>Date *</label>
            <input type="date" name="expense_date" required>

            <div class="button-group">
                <button type="submit" class="btn-primary">Add Expense</button>
                <a href="dashboard.php" class="btn-cancel">Cancel</a>
            </div>

        </form>
    </div>

</section>

</body>
</html>