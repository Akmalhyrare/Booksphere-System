<?php
// Database connection details
$host = "localhost";
$dbname = "onlinebookspheresystem";
$user = "postgres";
$password = "miera1234";

$dsn = "pgsql:host=$host;dbname=$dbname";

try {
    // Create a new PDO instance
    $pdo = new PDO($dsn, $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Enable exceptions on errors
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // Set default fetch mode to associative array
    ]);

    // If the connection is successful, you can perform further operations here
    // echo "Connected to the database successfully!";

} catch (PDOException $e) {
    // If an error occurs, output the error message
    die("An error occurred while connecting to the database: " . $e->getMessage());
}
?>