<?php
session_start();

// Database connection
$host = 'localhost';
$username = 'postgres';
$password = 'miera1234'; // Replace with your DB password
$dbname = 'booksphere'; // Replace with your DB name

// Create connection string
$conn = pg_connect("host=$host dbname=$dbname user=$username password=$password");

// Check connection
if (!$conn) {
    die("Connection failed: " . pg_last_error());
}

// Ensure the cart is not empty and an order has been placed
if (empty($_SESSION['cart']) || !isset($_SESSION['order_id'])) {
    die("No items in the cart or the order has not been placed yet!");
}

$orderID = $_SESSION['order_id']; // Get order ID from session

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get shipping details from the form
    $shippingAddress = $_POST['shipping_address'];
    $shippingMethod = $_POST['shipping_method'];
    $trackingNo = uniqid('TRK'); // Generate a unique tracking number
    $shippingDate = date('Y-m-d H:i:s');
    $status = 'Pending'; // Initial shipping status

    // Save shipping details into the database
    $query = "
        INSERT INTO SHIPPING 
        (Shipping_Address, Shipping_Method, TrackingNo, Shipping_Date, Status, Order_ID) 
        VALUES ($1, $2, $3, $4, $5, $6)
    ";

    $result = pg_query_params($conn, $query, [
        $shippingAddress, $shippingMethod, $trackingNo, $shippingDate, $status, $orderID
    ]);

    if ($result) {
        echo "<script>alert('Shipping details saved successfully! Tracking No: $trackingNo');</script>";
        echo "<p>Shipping details have been recorded. Your tracking number is <strong>$trackingNo</strong>.</p>";
        echo '<a href="catalog.php">Continue Shopping</a>';
    } else {
        echo "Failed to save shipping details: " . pg_last_error($conn);
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shipping Details</title>
</head>
<body>
    <h1>Shipping Details</h1>
    <form action="shipping.php" method="POST">
        <label for="shipping_address">Shipping Address:</label><br>
        <textarea name="shipping_address" id="shipping_address" required></textarea><br><br>

        <label for="shipping_method">Shipping Method:</label><br>
        <select name="shipping_method" id="shipping_method" required>
            <option value="Standard">Standard</option>
            <option value="Express">Express</option>
            <option value="Same-Day">Same-Day Delivery</option>
        </select><br><br>

        <button type="submit">Submit Shipping Details</button>
    </form>
</body>
</html>
