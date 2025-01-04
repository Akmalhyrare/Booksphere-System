<?php
session_start();

// Check if the user is logged in, otherwise redirect to login page
if (!isset($_SESSION['CustomerID'])) {
    header('Location: login.php');
    exit;
}

require_once "config.php";

// Fetch the logged-in user's username and email
$customerID = $_SESSION['CustomerID'];
$stmt = $pdo->prepare("SELECT CustUsername, CustEmail FROM CUSTOMER WHERE CustomerID = :customerID");
$stmt->execute(['customerID' => $customerID]);
$user = $stmt->fetch();

// Store the user information
$username = $user['CustUsername'];
$email = $user['CustEmail'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Booksphere</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* General Styles */
        body {
            background-color: #f4edca;
            font-family: Arial, sans-serif;
            margin: 0;
        }
        header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 20px;
            background-color: #ffe599;
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1000;
        }
        header nav a {
            color: #ce7e00;
            text-decoration: none;
            margin-right: 30px;
            font-size: 24px;
        }
        .profile-menu {
            position: relative;
            display: inline-block;
        }
        .profile-btn {
            background-color: #ce7e00;
            color: white;
            border: none;
            padding: 12px 25px;
            cursor: pointer;
            border-radius: 35px;
            font-size: 23px; 
            margin-right: 40px;
        }
        .dropdown-content { 
            display: none; 
            position: absolute; 
            background-color: rgb(237, 230, 194); 
            right: 0; 
            min-width: 300px; 
            border-radius: 30px; 
            padding: 12px; 
            font-size: 22px; 
        }
        .profile-menu:hover .dropdown-content {
            display: block;
        }

        /* Flexbox Layout for Content */
        .main-container {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin: 100px auto 0 auto;
            max-width: 1200px;
        }

        /* Left Content: Text */
        .text-content {
            flex: 1; /* Takes up half the space */
            margin-left: -25%; /* Move the text more to the left */
            padding-right: 600px; /* Add some spacing to the right */
            
        }
        .text-content h2 {
            font-size: 6rem;
            color: #333;
           
        }

        .text-content p {
            font-size: 3rem;
            color: #555;
            margin: 20px 10 30px;
            
        }
        .btn {
            background-color: #ce7e00;
            color: white;
            padding: 15px 25px;
            text-decoration: none;
            border-radius: 15px;
            font-size: 2rem;
            transition: background-color 0.3s ease;
        }
        .btn:hover {
            background-color: #007acc;
        }

        /* Right Content: Image */
        .image-content {
            flex: 1; /* Takes up equal space */
            display: flex;
            justify-content: flex-end; /* Move image to the extreme right */
            margin-right: 10%; /* Add margin for spacing */
            margin-top: 150px;
        }
        .image-content img {
            width: 800px; /* Set custom width */
            height: 600px; /* Set custom height */
            object-fit: cover; /* Ensures the image fits neatly inside the box */
            border-radius: 15px; /* Optional: Rounded corners */
            display: block; /* Ensures proper alignment */
        }
    </style>
</head>
<body>
    <header>
        <h1 style="color: #ce7e00; margin: 0;">Booksphere</h1>
        <nav>
            <a href="dashboard.php">Home</a>
            <a href="books.php">All Books</a>
            <a href="cart.php">Cart</a>
            <div class="profile-menu">
                <button class="profile-btn">Profile</button>
                <div class="dropdown-content">
                    <p>Username: <?php echo htmlspecialchars($username); ?></p>
                    <p>Email: <?php echo htmlspecialchars($email); ?></p>
                    <a href="profile.php">View Profile</a>
                    <a href="logout.php" style="color: red;">Logout</a>
                </div>
            </div>
        </nav>
    </header>
    <div class="main-container">
        <!-- Left Side: Text Content -->
        <div class="text-content">
            <h2>
                Welcome, 
                <span class="username"><?php echo htmlspecialchars($username);?><span>
                !
                </h2>
            <p>Discover your next great read.</p>
            <a href="books.php" class="btn">Discover Books</a>
        </div>
        <!-- Right Side: Image Content -->
        <div class="image-content">
            <img src="dashboardpic1.jpg" alt="Books Image">
        </div>
    </div>
</body>
</html>
