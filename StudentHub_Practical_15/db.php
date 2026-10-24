<?php
$host = "localhost";
$dbname = "studenthub";
$username = "root";
$password = "";

$dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false
];

try {
    $pdo = new PDO($dsn, $username, $password, $options);
} catch (PDOException $exception) {
    error_log("StudentHub database connection failed: " . $exception->getMessage());
    http_response_code(500);
    die("Database connection failed. Start MySQL in XAMPP and import database/studenthub.sql. Check db.php settings.");
}
