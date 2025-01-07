<?php
// Database connection details
$host = "localhost";
$dbname = "onlinebookspheresystem";
$user = "postgres";
$password = "miera1234";

// Attempt to connect to the PostgreSQL database
$connection = pg_connect("host=$host dbname=$dbname user=$user password=$password");

// Check the connection
if (!$connection) {
    die("An error occurred while connecting to the database: " . pg_last_error());
} else {
    echo "Connection to the database was successful!";
}
?>
