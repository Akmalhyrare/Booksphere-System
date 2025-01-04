<?php
session_start();
require_once "config.php";

// Check if the user is logged in, otherwise redirect to login page
if (!isset($_SESSION['CustomerID'])) {
    header('Location: login.php');
    exit;
}

// Fetch user information from the database
$customerID = $_SESSION['CustomerID'];
$stmt = $pdo->prepare("SELECT CustomerID, CustName, CustEmail, CustUsername, HomeAddress FROM CUSTOMER WHERE CustomerID = :customerID");
$stmt->execute(['customerID' => $customerID]);
$user = $stmt->fetch();

// If user data not found
if (!$user) {
    echo "User information not found.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Profile - Booksphere</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4edca;
            font-family: Arial, sans-serif;
            margin: 0;
        }
        header {
            background-color: #ffe599;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.2);
        }
        header h1 {
            color: #ce7e00;
            margin: 0;
        }
        nav a {
            color: #ce7e00;
            text-decoration: none;
            margin-right: 15px;
            font-size: 24px;
        }
        nav a:hover {
            color: #ce7e00;
        }
        .profile-container {
            margin: 50px auto;
            max-width: 600px;
            background:rgb(237, 230, 194);
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
        }
        .profile-container h2 {
            text-align: center;
            color: #ce7e00;
            margin-bottom: 30px;
        }
        .profile-container p {
            font-size: 20px;
            margin: 10px 0;
        }
        .btn-back {
            display: inline-block;
            margin-top: 20px;
            color: white;
            background-color: #ce7e00;
            padding: 10px 15px;
            text-decoration: none;
            border-radius: 5px;
        }
        .btn-back:hover {
            background-color: #005f99;
        }
    </style>
</head>
<body>
    <header>
        <h1>Booksphere</h1>
        <nav>
            <a href="dashboard.php">Home</a>
            <a href="books.php">All Books</a>
            <a href="cart.php">Cart</a>
            <a href="logout.php" style="color: red;">Logout</a>
        </nav>
    </header>

    <div class="profile-container">
        <h2>Your Profile</h2>
        <p><strong>Customer ID:</strong> <?php echo htmlspecialchars($user['CustomerID']); ?></p>
        <p><strong>Full Name:</strong> <?php echo htmlspecialchars($user['CustName']); ?></p>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($user['CustEmail']); ?></p>
        <p><strong>Username:</strong> <?php echo htmlspecialchars($user['CustUsername']); ?></p>
        <p><strong>Home Address:</strong> <?php echo nl2br(htmlspecialchars($user['HomeAddress'])); ?></p>
        <a href="dashboard.php" class="btn-back">Back to Dashboard</a>
    </div>
</body>
</html>