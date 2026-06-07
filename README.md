# 🚌 Bus Information Management System

A web-based transportation information platform designed to help users search bus routes, view route details, and manage personal accounts through a secure authentication system.

The application provides an easy-to-use interface for accessing transportation information while offering administrative tools for managing system data.

---

## 🚀 Features

### 🔐 User Authentication

* User Registration
* Secure Login
* Session Management
* Logout Functionality

### 👤 Profile Management

* View User Profile
* Update Account Information
* Personalized User Experience

### 🔍 Route Search

* Search Available Bus Routes
* Filter Transportation Information
* Quick Route Lookup

### 🚌 Route Details

* View Detailed Route Information
* Access Bus Information
* Explore Transportation Data

### 🔑 Password Recovery

* Forgot Password Functionality
* Password Reset Workflow
* Secure Account Recovery

### 🛠 Admin Panel

* Administrative Dashboard
* Manage Transportation Data
* User Monitoring and Control

---

## 🛠 Tech Stack

### Backend

* PHP

### Database

* MySQL

### Frontend

* HTML5
* CSS3
* JavaScript

### Authentication

* PHP Sessions

---

## 📂 Project Structure

```bash
Bus-Information/
│
├── index.php
├── home.php
├── login.php
├── logout.php
├── profile.php
│
├── search.php
├── route_details.php
│
├── forgot_password.php
├── reset_password.php
│
├── admin.php
│
├── auth_check.php
├── config.php
└── theme.js
```

---

## ⚙️ Installation

### Clone Repository

```bash
git clone https://github.com/Priyank910/Bus-Information.git
```

### Move Project to Web Server

Place the project inside:

```text
htdocs/      (XAMPP)
```

or

```text
www/         (WAMP)
```

---

## 🗄 Database Setup

1. Create a MySQL database.
2. Import the provided SQL file (if available).
3. Update database credentials inside:

```php
config.php
```

Example:

```php
$host = "localhost";
$user = "root";
$password = "";
$database = "bus_information";
```

---

## ▶️ Run Application

Start:

* Apache
* MySQL

Visit:

```text
http://localhost/Bus-Information
```

---

## 🎯 Core Modules

### Authentication Module

Handles login, logout, and session validation.

### Route Search Module

Allows users to search transportation routes efficiently.

### Route Details Module

Displays detailed route information and transportation data.

### Profile Module

Manages user information and account details.

### Password Recovery Module

Provides secure password reset functionality.

### Admin Module

Allows administrators to manage system resources and user access.

---

## 📚 Learning Outcomes

* PHP Web Development
* MySQL Database Integration
* Session-Based Authentication
* User Access Control
* CRUD Operations
* Form Handling
* Server-Side Validation
* Transportation Information Systems

---

## 🚀 Future Enhancements

* Real-Time Bus Tracking
* Google Maps Integration
* Route Optimization
* Online Ticket Booking
* Mobile Responsive UI
* Email Notifications
* Passenger Feedback System
* Admin Analytics Dashboard

---

## 📸 Screenshots

Add screenshots of:

### Login Page

### Search Routes

### Route Details

### User Profile

### Admin Dashboard

---

## 👨‍💻 Author

Priyank Chavda

GitHub: https://github.com/Priyank910

---

## 📄 License

MIT License
