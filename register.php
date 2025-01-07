<?php
require_once "mieraconnect.php"; // Include your database configuration file

// Initialize messages
$success_msg = '';
$error_msg = '';

// Function to generate the next CustomerID
function generateCustomerID($pdo) {
    // Fetch the last CustomerID
    $stmt = $pdo->query("SELECT CustomerID FROM CUSTOMER ORDER BY LENGTH(CustomerID) DESC, CustomerID DESC LIMIT 1");
    $lastID = $stmt->fetchColumn();

    if ($lastID) {
        // Extract numeric part from the last ID
        $num = (int)filter_var($lastID, FILTER_SANITIZE_NUMBER_INT);
        $newNum = $num + 1; // Increment by 1
        return 'M' . $newNum; // Combine 'M' with the new number
    } else {
        // If no records exist, start with M1
        return 'M1';
    }
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Generate a new CustomerID
    $CustomerID = generateCustomerID($pdo);

    // Get form data
    $CustName = htmlspecialchars($_POST['CustName']);
    $CustEmail = htmlspecialchars($_POST['CustEmail']);
    $CustUsername = htmlspecialchars($_POST['CustUsername']);
    $CustPassword = $_POST['CustPassword'];
    $HomeAddress = htmlspecialchars($_POST['HomeAddress']);

    // Insert data into the CUSTOMER table
    try {
        $sql = "INSERT INTO CUSTOMER (CustomerID, CustName, CustEmail, CustUsername, CustPassword, HomeAddress)
                VALUES (:CustomerID, :CustName, :CustEmail, :CustUsername, :CustPassword, :HomeAddress)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':CustomerID', $CustomerID);
        $stmt->bindParam(':CustName', $CustName);
        $stmt->bindParam(':CustEmail', $CustEmail);
        $stmt->bindParam(':CustUsername', $CustUsername);
        $stmt->bindParam(':CustPassword', $CustPassword);
        $stmt->bindParam(':HomeAddress', $HomeAddress);

        if ($stmt->execute()) {
            $success_msg = "Registration successful! Your Customer ID is: $CustomerID";
        } else {
            $error_msg = "Error: Could not register the user.";
        }
    } catch (PDOException $e) {
        $error_msg = "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - BOOKSPHERE</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4edca;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            background-color: rgb(230, 223, 187);
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 350px;
            text-align: center;
        }
        h2 {
            color: #007bff;
            margin-bottom: 20px;
            font-size: 26px;
        }
        .input-field {
            margin-bottom: 10px;
            text-align: left;
        }
        label {
            display: block;
            margin-bottom: 10px;
            color: #333;
        }
        input, textarea {
            width: 100%;
            padding: 15px; /* Adjust padding */
            box-sizing: border-box;
            border: 2px solid #ccc;
            border-radius: 6px;
            font-size: 20px; /* Updated font size */
            color: #333;
        }
        button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
            font-size: 18px;
        }
        button:hover {
            background-color: #0056b3;
        }
        .link {
            margin-top: 15px;
            font-size: 18px;
        }
        .link a {
            color: #007bff;
            text-decoration: none;
        }
        .link a:hover {
            text-decoration: underline;
        }
        .success {
            color: green;
            margin-bottom: 15px;
            font-size: 20px;
        }
        .error {
            color: red;
            margin-bottom: 15px;
            font-size: 20px;
        }
    </style>
</head>
<body>
<div class="container">
        <h2>Register to BOOKSPHERE</h2>

        <!-- Display success or error message -->
        <?php if ($success_msg): ?>
            <p class="success"><?php echo $success_msg; ?></p>
        <?php elseif ($error_msg): ?>
            <p class="error"><?php echo $error_msg; ?></p>
        <?php endif; ?>

        <!-- Registration form -->
        <form action="register.php" method="POST">
            <div class="input-field">
                <label for="CustName">Full Name</label>
                <input type="text" id="CustName" name="CustName" required>
            </div>
            <div class="input-field">
                <label for="CustEmail">Email</label>
                <input type="email" id="CustEmail" name="CustEmail" required>
            </div>
            <div class="input-field">
                <label for="CustUsername">Username</label>
                <input type="text" id="CustUsername" name="CustUsername" required>
            </div>
            <div class="input-field">
                <label for="CustPassword">Password</label>
                <input type="password" id="CustPassword" name="CustPassword" required>
            </div>
            <div class="input-field">
                <label for="HomeAddress">Home Address</label>
                <textarea id="HomeAddress" name="HomeAddress" rows="3" required></textarea>
            </div>
            <button type="submit">Register</button>
        </form>

        <div class="link">
            Already have an account? <a href="login.php">Login here</a>
        </div>
    </div>
</body>
</html>
