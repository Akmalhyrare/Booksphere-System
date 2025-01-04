<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to BOOKSPHERE</title>
    <style>
        /* Reset Default Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* Fullscreen Body with Clear Image */
        body {
            font-family: Arial, sans-serif;
            height: 150vh; /* Fullscreen height */
            background: url('interface1.jpg') no-repeat center center fixed;
            background-size: cover; /* Fullscreen clear background image */
            display: flex;
            align-items: center; /* Vertically center content */
            justify-content: center; /* Horizontally center content */
        }

        /* Buttons Container */
        .buttons {
            display: flex;
            gap: 10px; /* Add space between buttons */
        }

        /* Buttons Style */
        .button {
            padding: 10px 20px;
            font-size: 1.5rem;
            text-decoration: none;
            color: #fff;
            background-color: #007BFF;
            border-radius: 15px;
            transition: background-color 0.3s ease;
        }

        .button:hover {
            background-color: #0056b3;
        }

        .button.signup {
            background-color: #28a745;
        }

        .button.signup:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
    <!-- Buttons -->
    <div class="buttons">
        <a href="login.php" class="button">Login</a>
        <a href="register.php" class="button signup">Sign Up</a>
    </div>
</body>
</html>














