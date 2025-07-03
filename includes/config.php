<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'root'); 
define('DB_PASS', ''); // Blank for XAMPP/WAMP
define('DB_NAME', 'feedback_system');

// Session configuration (add these lines)
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 0); // Change to 1 if using HTTPS
ini_set('session.use_strict_mode', 1);
session_name('FEEDBACK_SYSTEM'); // Custom session name

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Create database connection
try {
    $pdo = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>