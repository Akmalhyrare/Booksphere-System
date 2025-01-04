<?php
$host = 'localhost';
$dbname = 'booksphere';
$user = 'root'; // XAMPP default user
$pass = ''; // XAMPP default password

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
