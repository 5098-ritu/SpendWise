<?php
session_start();
include("config.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

$user_id = $_SESSION['user_id'];

/* ======================
   SUMMARY DATA
====================== */

// Total Expenses
$totalQuery = $conn->query("SELECT SUM(amount) as total FROM expenses WHERE user_id=$user_id");
$totalData = $totalQuery->fetch_assoc();
$total = $totalData['total'] ?? 0;

// This Month
$monthQuery = $conn->query("SELECT SUM(amount) as month_total 
FROM expenses 
WHERE user_id=$user_id 
AND MONTH(expense_date)=MONTH(CURDATE()) 
AND YEAR(expense_date)=YEAR(CURDATE())");

$monthData = $monthQuery->fetch_assoc();
$month_total = $monthData['month_total'] ?? 0;

// Transactions Count
$countQuery = $conn->query("SELECT COUNT(*) as total_count FROM expenses WHERE user_id=$user_id");
$countData = $countQuery->fetch_assoc();
$total_transactions = $countData['total_count'] ?? 0;

// Average
$average = $total_transactions > 0 ? $total / $total_transactions : 0;

/* ======================
   PIE CHART DATA
====================== */

$categoryQuery = $conn->query("
SELECT c.name AS category, IFNULL(SUM(e.amount),0) AS total
FROM categories c
LEFT JOIN expenses e 
ON c.name = e.category 
AND e.user_id = $user_id
AND MONTH(e.expense_date) = MONTH(CURDATE())
AND YEAR(e.expense_date) = YEAR(CURDATE())
WHERE c.user_id = $user_id
GROUP BY c.name
");

$categories = [];
$categoryTotals = [];

if($categoryQuery){
    while($row = $categoryQuery->fetch_assoc()){
        $categories[] = $row['category'];
        $categoryTotals[] = $row['total'];
    }
}

/* ======================
   SPENDING TREND DATA
====================== */

$days = [];
$dayTotals = [];

$query = $conn->query("
SELECT DATE_FORMAT(expense_date,'%b') AS month,
SUM(amount) AS total
FROM expenses
WHERE user_id=$user_id
GROUP BY DATE_FORMAT(expense_date,'%b')
ORDER BY MIN(expense_date)
LIMIT 7
");

if($query){

while($row = $query->fetch_assoc()){

$days[] = $row['month'];
$dayTotals[] = $row['total'];

}

}

/* ======================
   WEEKLY COMPARISON
====================== */

$weeklyTotals = [];
$weekLabels = [];

// Get current week's Monday
$currentMonday = strtotime('monday this week');

// 🔥 CHANGE: only 4 weeks
for ($i = 3; $i >= 0; $i--) {

    // Week start (Monday)
    $start = date("Y-m-d", strtotime("-$i week", $currentMonday));

    // Week end (Sunday)
    $end = date("Y-m-d", strtotime("+6 days", strtotime($start)));

    // Label (Mar 04 - Mar 10)
    $label = date("M d", strtotime($start)) . " - " . date("M d", strtotime($end));
    $weekLabels[] = $label;

    // Query
    $query = $conn->query("
        SELECT SUM(amount) AS total 
        FROM expenses 
        WHERE user_id = $user_id
        AND expense_date BETWEEN '$start' AND '$end'
    ");

    $row = $query->fetch_assoc();
    $weeklyTotals[] = $row['total'] ? (float)$row['total'] : 0;
}


/* ======================
   TOP CATEGORIES
====================== */

$topCatQuery = $conn->query("
SELECT c.name AS category, IFNULL(SUM(e.amount),0) AS total
FROM categories c
LEFT JOIN expenses e 
ON c.name = e.category 
AND e.user_id = $user_id
WHERE c.user_id = $user_id
GROUP BY c.name
ORDER BY total DESC
LIMIT 4
");

$topCategories = [];

while($row = $topCatQuery->fetch_assoc()){
$topCategories[] = $row;
}
/* ======================
   LARGEST EXPENSE
====================== */

$largestQuery = $conn->query("SELECT MAX(amount) as largest FROM expenses WHERE user_id=$user_id");
$largestData = $largestQuery->fetch_assoc();
$largest = $largestData['largest'] ?? 0;

/* ======================
   RECENT TRANSACTIONS
====================== */

$recentQuery = $conn->query("SELECT * FROM expenses 
WHERE user_id=$user_id 
ORDER BY expense_date DESC 
LIMIT 5");

/* ======================
   BUDGET SYSTEM
====================== */

// Save Budget
if(isset($_POST['budget'])){
$amount = $_POST['budget'];
$month = date('m');
$year = date('Y');

$conn->query("
DELETE FROM budgets 
WHERE user_id=$user_id 
AND month=$month 
AND year=$year
");

$conn->query("
INSERT INTO budgets(user_id,amount,month,year)
VALUES($user_id,$amount,$month,$year)
");
}

// Fetch Budget
$budget = 0;

$q = $conn->query("
SELECT amount FROM budgets
WHERE user_id=$user_id
AND month=MONTH(CURDATE())
AND year=YEAR(CURDATE())
LIMIT 1
");

if($q && $q->num_rows > 0){
$budget = $q->fetch_assoc()['amount'];
}

// Calculate usage
$usagePercent = 0;
$budgetMsg = "No budget set";
$budgetClass = "";

if($budget > 0){

$usagePercent = ($month_total / $budget) * 100;

if($usagePercent >= 100){
$budgetMsg = "⚠️ You are overspending!";
$budgetClass = "danger";
}elseif($usagePercent >= 80){
$budgetMsg = "⚠️ You used ".round($usagePercent)."% of your budget";
$budgetClass = "warning";
}else{
$budgetMsg = "✅ You are within budget";
$budgetClass = "safe";
}
}

?>

<!DOCTYPE html>
<html>
<head>

<title>Dashboard - SpendWise</title>

<link rel="stylesheet" href="style.css">
<link rel="stylesheet" href="dashboard.css">

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

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

<section class="dashboard-container">

<h2>Dashboard</h2>
<p class="subtitle">Welcome back! Here's your expense overview</p>


<!-- SUMMARY CARDS -->

<div class="cards-grid">

<div class="card blue">
<div class="card-icon"><i class="fa-solid fa-wallet"></i></div>
<h4>Total Spent</h4>
<h2>₹ <?php echo number_format($total,2); ?></h2>
<p>all expenses</p>
</div>

<div class="card purple">
<div class="card-icon"><i class="fa-solid fa-calendar"></i></div>
<h4>This Month</h4>
<h2>₹ <?php echo number_format($month_total,2); ?></h2>
<p>spent this month</p>
</div>

<div class="card green">
<div class="card-icon"><i class="fa-solid fa-chart-line"></i></div>
<h4>Average/Transaction</h4>
<h2>₹ <?php echo number_format($average,2); ?></h2>
<p>average spending</p>
</div>

<div class="card orange">
<div class="card-icon"><i class="fa-solid fa-arrow-trend-up"></i></div>
<h4>Largest Expense</h4>
<h2>₹ <?php echo number_format($largest,2); ?></h2>
<p>biggest purchase</p>
</div>

</div>

<!-- BUDGET SECTION -->

<div class="budget-box <?php echo $budgetClass; ?>">

<div class="budget-top">

<div>
<h3>💰 Monthly Budget</h3>
<p>Track your spending limit</p>
</div>

<form method="POST" class="budget-form">
<input type="number" name="budget" placeholder="Set budget" required>
<button type="submit">Save</button>
</form>

</div>

<div class="budget-stats">
<span>Budget: ₹ <?php echo number_format($budget,2); ?></span>
<span>Spent: ₹ <?php echo number_format($month_total,2); ?></span>
</div>

<div class="progress-bar">
<div class="progress-fill" style="width:<?php echo min($usagePercent,100); ?>%"></div>
</div>

<p class="budget-msg"><?php echo $budgetMsg; ?></p>

</div>


<!-- FIRST ROW CHARTS -->

<div class="charts-grid">

<div class="chart-card">
<h3>Spending Trend</h3>
<canvas id="lineChart"></canvas>
</div>

<div class="chart-card categories">

<h3>Top Categories</h3>

<?php if(count($topCategories)==0): ?>

<p style="color:#6b7280;">No category data yet</p>

<?php else: ?>

<?php 
$max = max(array_column($topCategories,'total'));

$colors = ['red','blue','green','purple','pink','orange'];
$i = 0;
?>

<?php foreach($topCategories as $cat):

$percent = $max>0 ? ($cat['total']/$max)*100 : 0;
$color = $colors[$i % count($colors)];
$i++;

?>

<div class="category-row">

<div class="cat-info">
<i class="fa-solid fa-receipt"></i>
<span><?php echo $cat['category']; ?></span>
</div>

<div class="progress-bar">
<div class="progress <?php echo $color ?>" style="width:<?php echo $percent ?>%"></div>
</div>

</div>

<?php endforeach; ?>

<?php endif; ?>

</div>

</div>


<!-- SECOND ROW CHARTS -->

<div class="charts-row">

<div class="chart-card">
<h3>Weekly Comparison</h3>
<p class="chart-subtitle">Last 4 weeks spending</p>
<canvas id="barChart"></canvas>
</div>

<div class="chart-card">
<h3>Category Distribution</h3>
<p class="chart-subtitle">This month's breakdown</p>
<canvas id="pieChart"></canvas>
</div>

</div>


<!-- RECENT TRANSACTIONS (FULL WIDTH) -->

<div class="recent-card">

<h3>Recent Transactions</h3>
<p class="chart-subtitle">Latest expense activities</p>

<?php if($recentQuery && $recentQuery->num_rows > 0): ?>

<?php while($row = $recentQuery->fetch_assoc()): ?>

<div class="transaction">

<div class="left">

<div class="ticon">
<i class="fa-solid fa-receipt"></i>
</div>

<div>
<strong><?php echo htmlspecialchars($row['title']); ?></strong>
<p>
<?php echo htmlspecialchars($row['category']); ?> • 
<?php echo date("M d", strtotime($row['expense_date'])); ?>
</p>
</div>

</div>

<div class="amount">
- ₹ <?php echo number_format($row['amount'],2); ?>
</div>

</div>

<?php endwhile; ?>

<?php else: ?>

<p style="color:#6b7280;">No transactions yet</p>

<?php endif; ?>

</div>

</section>


<!-- CHARTS SCRIPT -->
<script>

/* SPENDING TREND */

new Chart(document.getElementById('lineChart'),{
type:'line',
data:{
labels: <?php echo json_encode($days); ?>,
datasets:[{
label:'Expenses',
data: <?php echo json_encode($dayTotals); ?>,
borderColor:'#3b82f6',
backgroundColor:'rgba(59,130,246,0.12)',
fill:true,
tension:0.45,
pointRadius:3,
pointBackgroundColor:'#3b82f6'
}]
},
options:{
plugins:{legend:{display:false}},
scales:{
y:{beginAtZero:true,grid:{color:'#e5e7eb'}},
x:{grid:{display:false}}
}
}
});


/* WEEKLY BAR */
new Chart(document.getElementById('barChart'),{
type:'bar',
data:{
labels: <?php echo json_encode($weekLabels); ?>,
datasets:[{
data: <?php echo json_encode($weeklyTotals); ?>,
backgroundColor:[
'#60a5fa',
'#34d399',
'#fbbf24',
'#f472b6'
],
borderRadius:10,
barThickness:36
}]
},
options:{
plugins:{legend:{display:false}},
scales:{
y:{grid:{color:'#e5e7eb'}},
x:{grid:{display:false}}
}
}
});

/* CATEGORY PIE */

new Chart(document.getElementById('pieChart'),{
type:'doughnut',
data:{
labels: <?php echo json_encode($categories); ?>,
datasets:[{
data: <?php echo json_encode($categoryTotals); ?>,
backgroundColor:[
'#ef4444',
'#3b82f6',
'#ec4899',
'#8b5cf6',
'#f59e0b'
]
}]
},
options:{
cutout:'70%',
plugins:{
legend:{position:'bottom'}
}
}
});

</script>

<script>
function toggleProfile(){
    const menu = document.getElementById("profileDropdown");

    if(menu.style.display === "block"){
        menu.style.display = "none";
    }else{
        menu.style.display = "block";
    }
}

/* FIX CLICK ISSUE */
document.getElementById("profileDropdown").addEventListener("click", function(e){
    e.stopPropagation();
});

/* close when clicking outside */
window.addEventListener("click", function(e){
    if(!e.target.closest(".profile-menu")){
        document.getElementById("profileDropdown").style.display = "none";
    }
});
</script>
<script>
function downloadPDF(){

const element = document.querySelector(".dashboard-container");

/* SAVE THEME */
const isDark = document.body.classList.contains("dark");

/* REMOVE DARK MODE FOR PDF */
document.body.classList.remove("dark");

/* FIX WIDTH FOR A4 */
element.style.width = "800px";
element.style.margin = "auto";

/* CONVERT CHARTS → IMAGES */
const canvases = document.querySelectorAll("canvas");
const images = [];

canvases.forEach((canvas, i) => {
    const img = document.createElement("img");
    img.src = canvas.toDataURL("image/png");
    img.style.width = "100%";
    img.style.marginBottom = "20px";
    images.push({canvas, img});
    canvas.parentNode.replaceChild(img, canvas);
});

/* PDF OPTIONS */
const opt = {
margin: 10,
filename: 'SpendWise_Dashboard.pdf',
image: { type: 'jpeg', quality: 1 },
html2canvas: { 
    scale: 2,
    useCORS: true
},
jsPDF: { 
    unit: 'mm', 
    format: 'a4', 
    orientation: 'portrait' 
},
pagebreak: { mode: ['avoid-all', 'css', 'legacy'] }
};

/* GENERATE PDF */
html2pdf().set(opt).from(element).save().then(() => {

/* RESTORE CHARTS */
images.forEach(obj => {
    obj.img.parentNode.replaceChild(obj.canvas, obj.img);
});

/* RESTORE THEME */
if(isDark){
    document.body.classList.add("dark");
}

/* RESET WIDTH */
element.style.width = "";
element.style.margin = "";

});


}
</script>
<script>

/* APPLY SAVED THEME */
if(localStorage.getItem("theme") === "dark"){
    document.body.classList.add("dark");
}

/* TOGGLE DARK MODE */
function toggleDarkMode(){
    document.body.classList.toggle("dark");

    if(document.body.classList.contains("dark")){
        localStorage.setItem("theme","dark");
    }else{
        localStorage.setItem("theme","light");
    }
}

</script>
</body>
</html>