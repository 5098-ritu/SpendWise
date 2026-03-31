💰 SpendWise

A full-stack web application designed to help users manage their personal finances efficiently. The system allows users to track expenses, manage categories, set budgets, and analyze spending patterns through dashboards and charts.

🔗 Live Demo

👉 https://spendwise.great-site.net

📖 Project Overview

SpendWise is a personal finance management system that helps users track their expenses, categorize spending, and analyze financial habits. It provides a clean dashboard, detailed analytics, and budgeting features to improve financial decision-making.

🚀 Key Features
💸 Expense Management
Add new expenses
Delete expenses
Track amount, category, and date
📂 Category Management
Add custom categories
Delete categories
View category-wise spending

📊 Dashboard
Total expenses overview
Monthly spending summary
Transaction insights

📈 Analytics
Monthly spending trends
Weekly and daily charts
Category-wise distribution
💰 Budget Management
Set monthly budgets
Track spending vs budget

⚙️ Additional Features
🌙 Dark mode
📄 Export analytics as PDF

🛠️ Technology Stack
Category  |	Technology
Frontend  |	HTML, CSS, JavaScript
Backend	  | PHP
Database  | MySQL
Charts	  | Chart.js
Server	  | XAMPP / WAMP
Deployment| InfinityFree

📂 Project Structure
SpendWise/
│
├── index.html
├── login.html
├── register.html
│
├── login.php
├── register.php
├── logout.php
├── config.php
│
├── dashboard.php
├── analytics.php
├── expenses.php
├── categories.php
│
├── add_expense.php
├── delete_expense.php
├── add_category.php
├── delete_category.php
│
├── style.css
├── dashboard.css
├── analytics.css
├── expenses.css
├── categories.css
├── add.css
├── add_expense.css
├── register.css
│
├── fintrack.sql   ✅ Database file

⚙️ Installation & Setup

Follow the steps below to run SpendWise on your local system:

📥 1. Clone the Repository
git clone https://github.com/your-username/spendwise.git
cd spendwise

🖥️ 2. Setup Local Environment
Install XAMPP or WAMP
Move the project folder to:
C:\xampp\htdocs\

🗄️ 3. Setup Database
🔹 Step 1: Open phpMyAdmin
Go to:
http://localhost/phpmyadmin

🔹 Step 2: Create Database
CREATE DATABASE fintrack;

🔹 Step 3: Import SQL File
Click on Import tab
Select file: fintrack.sql
Click Go

✅ Database Setup Complete
The following tables will be created automatically:

👤 users → Stores user details
💸 expenses → Stores all transactions
📂 categories → Stores expense categories
💰 budgets → Stores monthly budgets

🚀 4. Run the Project
Open your browser and visit:
http://localhost/spendwise

🌐 Usage
Register / Login
Add expenses
Manage categories
View dashboard
Analyze spending
Set budget

🔐 Security Features
Password hashing
Session-based login
Protected pages

💡 Future Enhancements
✏️ Edit Expense
🔔 Budget alerts
📧 Email notifications
🔁 Recurring expenses

👨‍💻 Author
Ritu Kumari

📜 License
For educational purposes only.

