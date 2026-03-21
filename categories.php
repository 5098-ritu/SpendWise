<?php
session_start();
include("config.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

$user_id = $_SESSION['user_id'];

/* Get category summary from categories table */
$query = "
SELECT 
    c.id,
    c.name,
    COUNT(e.id) as total_transactions,
    IFNULL(SUM(e.amount),0) as total_amount
FROM categories c
LEFT JOIN expenses e 
    ON c.name = e.category 
    AND e.user_id = $user_id
WHERE c.user_id = $user_id
GROUP BY c.id
";

$result = $conn->query($query);

/* Get overall total for percentage bar */
$totalQuery = $conn->query("
SELECT SUM(amount) as grand_total 
FROM expenses 
WHERE user_id = $user_id
");

$totalData = $totalQuery->fetch_assoc();
$grand_total = $totalData['grand_total'] ?? 0;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Categories - FinTrack</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="categories.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
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

<section class="category-container">

    <div class="category-header">
        <div>
            <h2>Categories</h2>
            <p>Manage your expense categories</p>
        </div>

        <a href="add_category.php" class="btn-add">+ Add Category</a>
    </div>

    <div class="category-grid">

        <?php 
        if($result && $result->num_rows > 0) {
            while($row = $result->fetch_assoc()) { 
                $percentage = ($grand_total > 0) 
                    ? ($row['total_amount'] / $grand_total) * 100 
                    : 0;
        ?>

        <div class="category-card">

            <div class="card-top">
                <?php
$icon = "fa-tag";

switch($row['name']){

case "Food":
$icon = "fa-utensils";
break;

case "Shopping":
$icon = "fa-bag-shopping";
break;

case "Transportation":
$icon = "fa-bus";
break;

case "Entertainment":
$icon = "fa-film";
break;

case "Bills":
$icon = "fa-file-invoice";
break;

}
?>

<div class="category-icon">
<i class="fa-solid <?php echo $icon ?>"></i>
</div>

                <a href="delete_category.php?id=<?php echo $row['id']; ?>" class="delete-btn">🗑</a>
            </div>

            <h3><?php echo $row['name']; ?></h3>

            <div class="meta">
                <span><?php echo $row['total_transactions']; ?> transactions</span>
                <span>₹ <?php echo number_format($row['total_amount'],2); ?></span>
            </div>

            <div class="progress-bar">
                <div class="progress" style="width: <?php echo $percentage; ?>%"></div>
            </div>

        </div>

        <?php 
            } 
        } else {
            echo "<p>No categories added yet.</p>";
        }
        ?>

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

const element = document.querySelector(".category-container");

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