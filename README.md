FitZone Gym is a full-stack web application built to streamline gym operations. It features a dual-interface system allowing administrators to manage memberships and staff, while providing gym members with a personal portal to log workouts and track daily attendance.

Key Features:

Admin Dashboard
Member Management: Register new members, view the current member directory, and remove expired accounts.
Subscription Assignment: Assign membership plans (Basic, Premium, Gold) with automated expiry date calculation.
Real-time Analytics: Track total, active, and expired memberships at a glance.
Staff Control: Create additional admin accounts with "Force Password Change" security.
User Portal
Workout Logger: Log exercises, sets, reps, and weights to track physical progress.
Attendance System: One-click daily check-in to maintain a digital log of gym visits.
Profile Security: Secure password management and personalized dashboards.

Technical Stack
Backend: PHP (Procedural with Prepared Statements for security)
Database: MySQL (InnoDB engine)
Frontend: HTML5, CSS3, Tailwind CSS, Font Awesome
Security: Bcrypt password hashing and session-based authentication

Installation & Database Setup:-

To get this project running locally, follow these instructions:

1. Prerequisites
Install a local server environment like XAMPP, WAMP, or MAMP.
Ensure PHP 7.4+ and MySQL are enabled.

2. Database Configuration
Open phpMyAdmin (http://localhost/phpmyadmin).
Create a new database named gym_db.
Click on the SQL tab and paste the following schema to create the tables:

SQL


-- 1. Create Users Table
CREATE TABLE IF NOT EXISTS `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `role` ENUM('admin', 'user') DEFAULT 'user',
  `must_change_password` TINYINT(1) DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 2. Create Members Table
CREATE TABLE IF NOT EXISTS `members` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `age` INT,
  `phone` VARCHAR(20),
  `plan` VARCHAR(50),
  `join_date` DATE,
  `expiry_date` DATE
) ENGINE=InnoDB;

-- 3. Create Workouts Table
CREATE TABLE IF NOT EXISTS `workouts` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT,
  `workout_date` DATE,
  `exercise` VARCHAR(100),
  `sets` INT,
  `reps` INT,
  `weight` DECIMAL(5,2),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 4. Create Attendance Table
CREATE TABLE IF NOT EXISTS `attendance` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT,
  `date` DATE,
  `time` TIME,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 5. Create User Plans Table
CREATE TABLE IF NOT EXISTS `user_plans` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT,
  `plan_name` VARCHAR(100),
  `start_date` DATE,
  `end_date` DATE,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;


3. Connect the Application
Ensure your db.php file matches your local database credentials:

PHP


$conn = mysqli_connect("localhost", "root", "", "gym_db");


License
This project is licensed under the MIT License - see the LICENSE file for details.
