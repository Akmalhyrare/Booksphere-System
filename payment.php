<?php
// Start a session
session_start();

// PostgreSQL database connection
$host = 'localhost';  // or IP address of the PostgreSQL server
$dbname = 'onlinebookspheresystem';  // replace with your database name
$username = 'postgres';  // replace with your PostgreSQL username
$password = 'miera1234';  // replace with your PostgreSQL password

// Create connection string
$conn = pg_connect("host=$host dbname=$dbname user=$username password=$password");

// Check connection
if (!$conn) {
    die("Connection failed: " . pg_last_error());
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve form data
    $order_id = $_POST['order_id'];
    $payment_type = $_POST['payment_type'];
    $payment_status = $_POST['payment_status'];
    $total_payment = $_POST['total_payment'];
    $payment_date = date("Y-m-d H:i:s");

    // Prepare the SQL query (do not include 'paymentid' since it is auto-generated)
    $sql = "INSERT INTO payment (order_id, payment_date, payment_type, payment_status, total_payment) 
            VALUES ($1, $2, $3, $4, $5)";

    // Prepare the statement
    $result = pg_prepare($conn, "insert_payment", $sql);

    // Check if preparation was successful
    if ($result) {
        // Execute the query
        $result = pg_execute($conn, "insert_payment", array($order_id, $payment_date, $payment_type, $payment_status, $total_payment));

        // Check if the query executed successfully
        if ($result) {
            // Generate a receipt
            $receipt = "Payment Receipt\n";
            $receipt .= "Order ID: " . $order_id . "\n";
            $receipt .= "Payment Date: " . $payment_date . "\n";
            $receipt .= "Payment Type: " . $payment_type . "\n";
            $receipt .= "Payment Status: " . $payment_status . "\n";
            $receipt .= "Total Payment: RM " . number_format($total_payment, 2) . "\n";

            // Display the receipt
            echo nl2br($receipt);

            // Optionally redirect or provide further actions
            echo "<a href='index.php'>Return to Home</a>";
        } else {
            echo "Error executing query: " . pg_last_error($conn);
        }
    } else {
        echo "Error preparing query: " . pg_last_error($conn);
    }

    // Close the connection
    pg_close($conn);
} else {
    // Display payment form
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Payment Form</title>
        <link rel="stylesheet" href="styles.css"> <!-- Link to your CSS file -->
    </head>
    <body>
        <div class="login-container">
            <h2 class="heading">Complete Your Payment</h2>
            <form method="POST" action="payment.php">
                <label for="order_id">Order ID:</label>
                <input type="number" name="order_id" id="order_id" required><br><br>
                
                <label for="payment_type">Payment Type:</label>
                <select name="payment_type" id="payment_type" required>
                    <option value="Cash">Cash</option>
                    <option value="Credit Card">Credit Card</option>
                    <option value="Online Banking">Online Banking</option>
                    <option value="Digital Wallet">Digital Wallet</option>
                </select><br><br>
                
                <label for="payment_status">Payment Status:</label>
                <select name="payment_status" id="payment_status" required>
                    <option value="Pending">Pending</option>
                    <option value="Completed">Completed</option>
                    <option value="Failed">Failed</option>
                </select><br><br>
                
                <label for="total_payment">Total Payment (RM):</label>
                <input type="number" name="total_payment" id="total_payment" step="0.01" required><br><br>
                
                <button type="submit">Submit Payment</button>
            </form>
        </div>
    </body>
    </html>
    <?php
}
?>
