<?php
/**
 * Practical 8: MySQL Schema Design, ER Model, PDO & MySQLi Connectivity
 * Subject: Web Development Frameworks (ITUE203) - Semester 3
 * Database Connection configuration for XAMPP
 */

$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "studenthub";

// 1. MySQLi Connection (Used in Practicals 9, 10, 11, 12, 13, 14)
$conn = new mysqli($servername, $username, $password, $dbname);

// Check if studenthub database exists, if not fallback to pract8
if ($conn->connect_error) {
    // Attempt fallback to pract8
    $conn = new mysqli($servername, $username, $password, "pract8");
    if ($conn->connect_error) {
        die("Database Connection Failed: " . $conn->connect_error);
    }
}

// Set charset to utf8mb4
$conn->set_charset("utf8mb4");

// 2. PDO Connection (Practical 8 Extension Requirement)
try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8mb4", $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    try {
        $pdo = new PDO("mysql:host=$servername;dbname=pract8;charset=utf8mb4", $username, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);
    } catch (PDOException $ex) {
        $pdo = null; // PDO optional fallback
    }
}
?>
