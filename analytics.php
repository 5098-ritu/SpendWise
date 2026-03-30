<?php
session_start();
include("config.php");

if(!isset($_SESSION['user_id'])){
header("Location: login.html");
exit();
}

$user_id=$_SESSION['user_id'];


/* TOTAL EXPENSE */

$total_expense=0;

$q=$conn->query("
SELECT IFNULL(SUM(amount),0) total
FROM expenses
WHERE user_id=$user_id
AND type='expense'
");

if($q){
$total_expense=$q->fetch_assoc()['total'];
}



/* CURRENT MONTH */

$current_month=0;

$q=$conn->query("
SELECT IFNULL(SUM(amount),0) total
FROM expenses
WHERE user_id=$user_id
AND type='expense'
AND MONTH(expense_date)=MONTH(CURDATE())
AND YEAR(expense_date)=YEAR(CURDATE())
");

if($q){
$current_month=$q->fetch_assoc()['total'];
}



/* AVERAGE MONTHLY */

$avg_month=0;

$q=$conn->query("
SELECT IFNULL(AVG(month_total),0) avg_total
FROM(
SELECT SUM(amount) month_total
FROM expenses
WHERE user_id=$user_id
AND type='expense'
GROUP BY YEAR(expense_date),MONTH(expense_date)
)t
");

if($q){
$avg_month=$q->fetch_assoc()['avg_total'];
}



/* CATEGORY DATA */

$categories=[];
$categoryTotals=[];

$q=$conn->query("
SELECT category,SUM(amount) total
FROM expenses
WHERE user_id=$user_id
AND type='expense'
GROUP BY category
ORDER BY total DESC
");

if($q){
while($r=$q->fetch_assoc()){
$categories[]=$r['category'];
$categoryTotals[]=$r['total'];
}
}



/* MONTH TREND */

$months = [];
$monthTotals = [];

/* LAST 6 MONTHS FIXED ARRAY */
for($i = 7; $i >= 0; $i--){

    $monthLabel = date("M", strtotime("-$i months"));
    $monthNum   = date("m", strtotime("-$i months"));
    $yearNum    = date("Y", strtotime("-$i months"));

    $months[] = $monthLabel;

    $q = $conn->query("
    SELECT IFNULL(SUM(amount),0) as total
    FROM expenses
    WHERE user_id=$user_id
    AND type='expense'
    AND MONTH(expense_date)='$monthNum'
    AND YEAR(expense_date)='$yearNum'
    ");

    if($q){
        $row = $q->fetch_assoc();
        $monthTotals[] = $row['total'];
    }else{
        $monthTotals[] = 0;
    }
}



/* WEEK TREND */

$weeks=[];
$weekTotals=[];

$q=$conn->query("
SELECT WEEK(expense_date,1) wk,SUM(amount) total
FROM expenses
WHERE user_id=$user_id
AND type='expense'
GROUP BY YEAR(expense_date),WEEK(expense_date,1)
ORDER BY YEAR(expense_date) DESC,WEEK(expense_date,1) DESC
LIMIT 8
");

if($q){
while($r=$q->fetch_assoc()){
$weeks[]="W".$r['wk'];
$weekTotals[]=$r['total'];
}
}

$weeks=array_reverse($weeks);
$weekTotals=array_reverse($weekTotals);



/* DAY OF WEEK */

$dayTotals=[0,0,0,0,0,0,0];

$q=$conn->query("
SELECT DAYOFWEEK(expense_date) d,SUM(amount) total
FROM expenses
WHERE user_id=$user_id
AND type='expense'
GROUP BY DAYOFWEEK(expense_date)
");

if($q){
while($r=$q->fetch_assoc()){
$index=$r['d']-1;
$dayTotals[$index]=$r['total'];
}
}



$hasData=count($categoryTotals)>0;


$avg_monthly = 0;

$avgQuery = $conn->query("
SELECT IFNULL(AVG(month_total),0) avg_total
FROM(
SELECT SUM(amount) month_total
FROM expenses
WHERE user_id=$user_id
GROUP BY YEAR(expense_date),MONTH(expense_date)
) m
");

if($avgQuery){
$row = $avgQuery->fetch_assoc();
$avg_monthly = $row['avg_total'];
}

/* ================= ADVANCED SMART INSIGHTS ================= */

// Top Category
$topCategory = !empty($categories) ? $categories[0] : "No data";


// Monthly Comparison
$currentMonthTotal = $current_month;
$lastMonthTotal = 0;

$q = $conn->query("
SELECT IFNULL(SUM(amount),0) total
FROM expenses
WHERE user_id=$user_id
AND type='expense'
AND MONTH(expense_date)=MONTH(CURDATE()-INTERVAL 1 MONTH)
AND YEAR(expense_date)=YEAR(CURDATE()-INTERVAL 1 MONTH)
");

if($q){
$lastMonthTotal = $q->fetch_assoc()['total'];
}

$trendText = "No previous data";
$trendClass = "neutral";

if($lastMonthTotal > 0){

$percentChange = (($currentMonthTotal - $lastMonthTotal)/$lastMonthTotal)*100;

if($percentChange > 0){
$trendText = "Your spending increased by ".round($percentChange)."% compared to last month.";
$trendClass = "bad";
}elseif($percentChange < 0){
$trendText = "Great! Your spending decreased by ".abs(round($percentChange))."% this month.";
$trendClass = "good";
}else{
$trendText = "Your spending stayed the same as last month.";
}
}


// Highest Spending Day
$topDay = "No data";

$q = $conn->query("
SELECT DAYNAME(expense_date) day, SUM(amount) total
FROM expenses
WHERE user_id=$user_id
AND type='expense'
GROUP BY DAYNAME(expense_date)
ORDER BY total DESC
LIMIT 1
");

if($q && $q->num_rows > 0){
$row = $q->fetch_assoc();
$topDay = $row['day'];
}


// Smart Suggestion
$suggestion = "Keep tracking your expenses regularly.";

if($trendClass == "bad"){
$suggestion = "You are spending more than usual. Try reducing unnecessary expenses.";
}elseif($trendClass == "good"){
$suggestion = "Nice work! You are managing your expenses well.";
}
?>



<!DOCTYPE html>
<html>
<head>

<title>Analytics</title>

<link rel="stylesheet" href="style.css">
<link rel="stylesheet" href="analytics.css">

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

<section class="analytics-container">

<h2>Analytics & Insights</h2>
<p class="subtitle">Comprehensive analysis of your spending patterns</p>



<!-- SUMMARY CARDS -->

<div class="summary-grid">

<div class="summary-card card-blue">
<span class="card-icon">📅</span>
<h4>Current Month</h4>
<h3>₹ <?php echo number_format($current_month,2); ?></h3>
</div>

<div class="summary-card card-purple">
<span class="card-icon">📊</span>
<h4>Average Monthly</h4>
<h3>₹ <?php echo number_format($avg_monthly,2); ?></h3>
</div>

<div class="summary-card card-green">
<span class="card-icon">💰</span>
<h4>Total Expenses</h4>
<h3>₹ <?php echo number_format($total_expense,2); ?></h3>
</div>

<div class="summary-card card-orange">
<span class="card-icon">🏆</span>
<h4>Top Category</h4>
<h3><?php echo !empty($categories)?$categories[0]:"None"; ?></h3>
</div>

</div>

<div class="insight-grid">

<div class="insight-card">
<div class="insight-icon">💡</div>
<h4>Top Spending Habit</h4>
<p>You mostly spend on <strong><?php echo $topCategory; ?></strong>.</p>
</div>

<div class="insight-card <?php echo $trendClass; ?>">
<div class="insight-icon">📈</div>
<h4>Monthly Trend</h4>
<p><?php echo $trendText; ?></p>
</div>

<div class="insight-card">
<div class="insight-icon">📅</div>
<h4>Spending Pattern</h4>
<p><?php echo $topDay; ?> is your highest spending day.</p>
</div>

<div class="insight-card highlight">
<div class="insight-icon">🤖</div>
<h4>Smart Suggestion</h4>
<p><?php echo $suggestion; ?></p>
</div>

</div>

<!-- MONTH TREND -->

<div class="chart-card">

<h3>8-Month Spending Trend</h3>

<canvas id="trendChart"></canvas>

</div>



<!-- CATEGORY + PIE -->

<div class="two-col">

<div class="chart-card">

<h3>Month-over-Month Comparison</h3>

<canvas id="barChart"></canvas>

</div>


<div class="chart-card">

<h3>Spending Distribution</h3>

<canvas id="pieChart"></canvas>

</div>

</div>



<!-- WEEK + DAY -->

<div class="two-col">

<div class="chart-card">

<h3>8-Week Spending Pattern</h3>

<canvas id="weekChart"></canvas>

</div>


<div class="chart-card">

<h3>Spending by Day of Week</h3>

<canvas id="dayChart"></canvas>

</div>

</div>



<!-- CATEGORY ANALYSIS -->
<?php
/* ICONS FOR CATEGORIES */
function getCategoryIcon($category){
$icons = [
"Food"=>"🍔",
"Transportation"=>"🚗",
"Entertainment"=>"🎬",
"Education"=>"📚",
"Shopping"=>"🛍️",
"Bills"=>"💡",
"Health"=>"🏥",
"Travel"=>"✈️"
];

return $icons[$category] ?? "📦";
}

/* COLORS FOR PROGRESS BARS */
$colors = ["#3b82f6","#10b981","#f59e0b","#ef4444","#8b5cf6","#14b8a6"];

$total = array_sum($categoryTotals);
?>

<div class="chart-card category-analysis">

<h3>Detailed Category Analysis</h3>
<p class="analysis-sub">Complete breakdown of all spending categories</p>

<?php if($total > 0): ?>

<?php foreach($categories as $i => $cat):

$amount = $categoryTotals[$i];
$percent = ($amount/$total)*100;
$color = $colors[$i % count($colors)];
?>

<div class="category-item">

<div class="category-top">

<div class="category-left">

<div class="cat-icon">
<?php echo getCategoryIcon($cat); ?>
</div>

<div class="cat-text">
<strong><?php echo $cat; ?></strong>
<span>₹<?php echo number_format($amount,2); ?></span>
</div>

</div>

<div class="cat-percent">
<?php echo number_format($percent,1); ?>%
</div>

</div>

<div class="progress-bar">
<div class="progress-fill" style="width:<?php echo $percent;?>%; background:<?php echo $color;?>;"></div>
</div>

</div>

<?php endforeach; ?>

<?php else: ?>

<p class="no-data">No expenses recorded yet.</p>

<?php endif; ?>

</div>

</section>



<script>


/* MONTH TREND */

new Chart(document.getElementById('trendChart'),{

type:'line',

data:{
labels: <?php echo json_encode(!empty($months)?$months:['No Data']); ?>,

datasets:[{
data: <?php echo json_encode(!empty($monthTotals)?$monthTotals:[0]); ?>,
borderColor:'#3b82f6',
backgroundColor:'rgba(59,130,246,0.2)',
fill:true,
tension:0.4
}]
},

options:{
responsive:true,
maintainAspectRatio:false
}

});



/* BAR CHART */

new Chart(document.getElementById('barChart'),{

type:'bar',

data:{
labels: <?php echo json_encode(!empty($categories)?$categories:['None']); ?>,

datasets:[{
data: <?php echo json_encode(!empty($categoryTotals)?$categoryTotals:[0]); ?>,
backgroundColor:'#3b82f6'
}]
},

options:{
responsive:true,
maintainAspectRatio:false
}

});



/* PIE */
new Chart(document.getElementById('pieChart'),{

type:'pie',

data:{
labels: <?php echo json_encode(!empty($categories)?$categories:['None']); ?>,

datasets:[{
data: <?php echo json_encode(!empty($categoryTotals)?$categoryTotals:[1]); ?>,

backgroundColor:[
'#3b82f6',
'#ef4444',
'#10b981',
'#f59e0b',
'#8b5cf6',
'#14b8a6'
]
}]
},

options:{
responsive:true,
maintainAspectRatio:false
}

});
/* WEEK CHART */

new Chart(document.getElementById('weekChart'),{

type:'line',

data:{
labels: <?php echo json_encode(!empty($weeks)?$weeks:['W1']); ?>,

datasets:[{
data: <?php echo json_encode(!empty($weekTotals)?$weekTotals:[0]); ?>,
borderColor:'#10b981',
backgroundColor:'rgba(16,185,129,0.2)',
fill:true,
tension:0.4
}]
},
options:{
responsive:true,
maintainAspectRatio:false
}

});



/* DAY CHART */

new Chart(document.getElementById('dayChart'),{

type:'bar',

data:{
labels:['Sun','Mon','Tue','Wed','Thu','Fri','Sat'],

datasets:[{
data: <?php echo json_encode($dayTotals); ?>,
backgroundColor:'#f59e0b'

}]
},
options:{
responsive:true,
maintainAspectRatio:false
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
function downloadPDF(){

const element = document.querySelector(".analytics-container");

/* Save dark mode */
const isDark = document.body.classList.contains("dark");
document.body.classList.remove("dark");

/* FIX WIDTH FOR A4 */
element.style.width = "800px";   // ✔ PERFECT width
element.style.margin = "auto";

/* FIX CHARTS → convert canvas to images */
document.querySelectorAll("canvas").forEach(canvas => {
    const img = document.createElement("img");
    img.src = canvas.toDataURL("image/png");
    img.style.width = "100%";
    img.style.marginBottom = "20px";
    canvas.parentNode.replaceChild(img, canvas);
});

const opt = {
margin: 10,
filename: 'SpendWise_Report.pdf',
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

html2pdf().set(opt).from(element).save().then(() => {

/* restore dark mode */
if(isDark){
document.body.classList.add("dark");
}

/* reset width */
element.style.width = "";
element.style.margin = "";

location.reload(); // ✔ restore charts back

});

}
</script>
</body>
</html>