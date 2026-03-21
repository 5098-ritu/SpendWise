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
<a href="add_expense.php">Add Expense</a>
<a href="expenses.php">Expenses</a>
<a href="categories.php">Categories</a>
<a href="analytics.php">Analytics</a>

<!-- PROFILE ICON ONLY -->
<div class="profile-menu">

<div class="profile-icon" onclick="toggleProfile()">
<?php echo strtoupper(substr($_SESSION['username'],0,1)); ?>
</div>

<div class="profile-dropdown" id="profileDropdown">

<div class="profile-header">
<strong><?php echo $_SESSION['username']; ?></strong>
<p><?php echo $_SESSION['email'] ?? ''; ?></p>
</div>

<hr>

<button onclick="toggleDarkMode()">🌙 Dark Mode</button>
<button onclick="downloadPDF()">📄 Export PDF</button>

<hr>

<a href="logout.php" class="logout-btn">Logout</a>

</div>

</div>

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

<script>

function toggleProfile(){
const menu = document.getElementById("profileDropdown");

if(menu.style.display === "block"){
menu.style.display = "none";
}else{
menu.style.display = "block";
}
}

/* close when clicking outside */
window.addEventListener("click", function(e){
if(!e.target.closest(".profile-menu")){
document.getElementById("profileDropdown").style.display = "none";
}
});

</script>

<script>

/* APPLY SAVED THEME */
if(localStorage.getItem("theme")==="dark"){
document.body.classList.add("dark");
}

/* TOGGLE */
function toggleDarkMode(){
document.body.classList.toggle("dark");

if(document.body.classList.contains("dark")){
localStorage.setItem("theme","dark");
}else{
localStorage.setItem("theme","light");
}
}


</script>

<script>
function downloadPDF(){

const element = document.querySelector(".add-container");

/* TEMP FIX FOR PDF */
document.body.classList.remove("dark"); // remove dark for clean PDF

const opt = {
margin: 0.3,
filename: 'SpendWise_Report.pdf',
image: { type: 'jpeg', quality: 1 },
html2canvas: { scale: 2, useCORS: true },
jsPDF: { unit: 'in', format: 'a4', orientation: 'portrait' }
};

html2pdf().set(opt).from(element).save().then(() => {

/* restore theme after download */
if(localStorage.getItem("theme")==="dark"){
document.body.classList.add("dark");
}

});

}
</script>
</body>
</html>