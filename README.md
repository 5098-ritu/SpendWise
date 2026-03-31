# 💰 SpendWise

A full-stack web application designed to help users manage their personal finances efficiently. Track expenses, manage categories, set budgets, and analyze spending patterns — all in one place.

---

## 🔗 Live Demo
👉 https://spendwise.great-site.net

---

## 📖 Project Overview

SpendWise is a personal finance management system that helps users monitor their spending habits, organize expenses into categories, and make better financial decisions through interactive dashboards and analytics.

---

## 🚀 Features

### 💸 Expense Management
- Add and delete expenses  
- Track amount, category, and date  

### 📂 Category Management
- Create and delete custom categories  
- View category-wise spending  

### 📊 Dashboard
- Total expense overview  
- Monthly summaries  
- Quick insights  

### 📈 Analytics
- Monthly trends  
- Weekly & daily charts  
- Category distribution  

### 💰 Budget Management
- Set monthly budgets  
- Track spending vs budget  

---

## ⚙️ Extra Features
- 🌙 Dark Mode  
- 📄 Export analytics as PDF  

---

## 🛠️ Tech Stack

| Layer       | Technology              |
|------------|------------------------|
| Frontend   | HTML, CSS, JavaScript  |
| Backend    | PHP                    |
| Database   | MySQL                  |
| Charts     | Chart.js               |
| Server     | XAMPP / WAMP           |
| Deployment | InfinityFree           |

---

## 📂 Project Structure

```
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
└── fintrack.sql
```

---

## ⚙️ Setup Instructions

### 1️⃣ Clone Repository
```bash
git clone https://github.com/your-username/spendwise.git
cd spendwise
```

### 2️⃣ Setup Environment
- Install **XAMPP / WAMP**
- Move project folder to:
```
C:\xampp\htdocs\
```

### 3️⃣ Database Setup

Open phpMyAdmin:
```
http://localhost/phpmyadmin
```

Create database:
```sql
CREATE DATABASE fintrack;
```

Then:
- Go to **Import**
- Select `fintrack.sql`
- Click **Go**

---

### ✅ Tables Created
- users  
- expenses  
- categories  
- budgets  

---

### 4️⃣ Run Project
```
http://localhost/spendwise
```

---

## 🌐 Usage
- Register / Login  
- Add expenses  
- Manage categories  
- View dashboard  
- Analyze spending  
- Set budget  

---

## 🔐 Security
- Password hashing  
- Session-based authentication  
- Protected routes  

---

## 💡 Future Improvements
- Edit expenses  
- Budget alerts  
- Email notifications  
- Recurring expenses  

---

## 👨‍💻 Author
**Ritu Kumari**

---

## 📜 License
For educational purposes only.