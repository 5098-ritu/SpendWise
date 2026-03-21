<?php
session_start();
include("config.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

$user_id = $_SESSION['user_id'];

/* FILTER VALUES */

$search = isset($_GET['search']) ? $_GET['search'] : '';
$category = isset($_GET['category']) ? $_GET['category'] : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'DESC';

/* BUILD QUERY */

$query = "SELECT * FROM expenses WHERE user_id='$user_id'";

if(!empty($search)){
$query .= " AND (title LIKE '%$search%' OR category LIKE '%$search%')";
}

if(!empty($category)){
$query .= " AND category='$category'";
}

$query .= " ORDER BY expense_date $sort";

$result = $conn->query($query);

/* TOTAL + COUNT */

$countQuery = $conn->query("
SELECT COUNT(*) as total_count, SUM(amount) as total_amount
FROM expenses
WHERE user_id='$user_id'
");

$countData = $countQuery->fetch_assoc();

$total_transactions = $countData['total_count'] ?? 0;
$total_amount = $countData['total_amount'] ?? 0;
?>

<!DOCTYPE html>
<html>
<head>
<title>Expenses - SpendWise</title>

<link rel="stylesheet" href="style.css">
<link rel="stylesheet" href="expenses.css">

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

<section class="expenses-container">

<h2>All Expenses</h2>

<p class="sub-info">
<?php echo $total_transactions; ?> transactions • 
Total: ₹ <?php echo number_format($total_amount,2); ?>
</p>

<!-- FILTER BAR -->

<div class="filters">

<form method="GET">

<input type="text" name="search" placeholder="Search expenses..." value="<?php echo htmlspecialchars($search); ?>">

<select name="category">
<option value="">All Categories</option>
<option value="Food">Food</option>
<option value="Shopping">Shopping</option>
<option value="Transportation">Transportation</option>
<option value="Entertainment">Entertainment</option>
<option value="Bills">Bills</option>
</select>

<select name="sort">

<option value="DESC" <?php if($sort=="DESC") echo "selected"; ?>>Newest First</option>

<option value="ASC" <?php if($sort=="ASC") echo "selected"; ?>>Oldest First</option>

</select>

<button type="submit">Apply</button>

</form>

</div>

<!-- TABLE -->

<div class="expenses-table">

<div class="table-header">
<div>Expense</div>
<div>Category</div>
<div>Date</div>
<div>Amount</div>
<div>Action</div>
</div>

<?php if($result->num_rows > 0): ?>

<?php while($row = $result->fetch_assoc()){

$icon = "fa-receipt";

switch($row['category']){

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

<div class="table-row">

<div class="expense-info">

<div class="expense-icon">
<i class="fa-solid <?php echo $icon ?>"></i>
</div>

<strong><?php echo $row['title']; ?></strong>

</div>

<div>
<span class="badge <?php echo strtolower(str_replace(' ','-',$row['category'])); ?>">
<?php echo $row['category']; ?>
</span>
</div>

<div>
<?php echo date("M d, Y", strtotime($row['expense_date'])); ?>
</div>

<div class="amount">
₹ <?php echo number_format($row['amount'],2); ?>
</div>

<div>
<a href="delete_expense.php?id=<?php echo $row['id']; ?>" class="delete-btn">🗑</a>
</div>

</div>

<?php } ?>

<?php else: ?>

<p style="padding:20px;">No expenses found</p>

<?php endif; ?>

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

const element = document.querySelector(".dashboard-container");

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