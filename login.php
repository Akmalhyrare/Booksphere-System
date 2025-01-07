<?php
session_start();

// If the user is already logged in, redirect to the dashboard
if (isset($_SESSION['CustomerID'])) {
    header('Location: dashboard.php');
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    require_once "mieraconnect.php";

    $username = $_POST['username'];
    $password = $_POST['password'];

    // Fetch user data
    $stmt = $pdo->prepare("SELECT * FROM CUSTOMER WHERE CustUsername = :username");
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch();

    // Verify password
    if ($user && $password === $user['CustPassword']) {
        $_SESSION['CustomerID'] = $user['CustomerID'];
        header('Location: dashboard.php');
        exit;
    } else {
        $error = "Invalid username or password";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - BOOKSPHERE</title>
    <style>
        body {
            background-color: #f4edca;
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .login-container {
            background-color: rgb(230, 223, 187);
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            text-align: center;
            width: 350px;
        }
    
        .heading {
            font-size: 30px;
            color: #007bff;
            margin-bottom: 20px;
        }
        input {
            width: 100%;
            padding: 12px;
            margin-bottom: 20px; /* Spacing between input fields */
            font-size: 20px;
            border: 1px solid #ccc;
            border-radius: 8px;
            box-sizing: border-box;
        }
        button {
            width: 100%;
            padding: 12px;
            font-size: 18px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 8px;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
        .register-link {
            margin-top: 15px;
            font-size: 18px;
        }
        .register-link a {
            color: #007bff;
            text-decoration: none;
        }
        .register-link a:hover {
            text-decoration: underline;
        }
        .error {
            color: red;
            margin-bottom: 18px;
            font-size: 20px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2 class="heading">Login to <strong>BOOKSPHERE</strong></h2>

        <!-- Error message -->
        <?php if (isset($error)) { echo "<p class='error'>$error</p>"; } ?>

        <!-- Login Form -->
        <form method="POST" action="login.php">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>

        <p class="register-link">New user? <a href="register.php">Register here</a></p>
    </div>
</body>
</html>
