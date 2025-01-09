<?php
// Start a session
session_start();

// PostgreSQL database connection
$host = 'localhost';  // or IP address of the PostgreSQL server
$dbname = 'booksphere';  // replace with your database name
$username = 'postgres';  // replace with your PostgreSQL username
$password = 'miera1234';  // replace with your PostgreSQL password

// Create connection string
$conn = pg_connect("host=$host dbname=$dbname user=$username password=$password");

// Check connection
if (!$conn) {
    die("Connection failed: " . pg_last_error());
}

// Function to calculate total payment based on order details
function calculateTotalPayment($conn, $order_id) {
    // Fetch order items
    $sql = "SELECT book.book_price, orderlist.quantity
            FROM orderlist
            JOIN book ON orderlist.book_isbn = book.book_isbn
            WHERE orderlist.order_id = $1";
    
    $result = pg_prepare($conn, "fetch_order_items", $sql);
    $result = pg_execute($conn, "fetch_order_items", array($order_id));

    $total_payment = 0.0;

    // Calculate total payment
    while ($row = pg_fetch_assoc($result)) {
        $total_payment += $row['book_price'] * $row['quantity'];
    }

    return $total_payment;
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['order_id'])) {
        // Step 1: Fetch order details and calculate total payment
        $order_id = $_POST['order_id'];
        $total_payment = calculateTotalPayment($conn, $order_id);
        $_SESSION['order_id'] = $order_id;
        $_SESSION['total_payment'] = $total_payment;
        header("Location: payment.php?step=2");
        exit();
    } elseif (isset($_POST['payment_type'])) {
        // Step 2: Capture payment details based on selected payment type
        $payment_type = $_POST['payment_type'];
        $_SESSION['payment_type'] = $payment_type;
        header("Location: payment.php?step=3");
        exit();
    } elseif (isset($_POST['payment_details'])) {
        // Step 3: Process the payment and display a receipt
        $order_id = $_SESSION['order_id'];
        $total_payment = $_SESSION['total_payment'];
        $payment_type = $_SESSION['payment_type'];
        $payment_status = 'Pending'; // Default status
        $payment_date = date("Y-m-d H:i:s");

        // Capture additional payment details
        if ($payment_type === 'Credit Card') {
            $card_number = $_POST['card_number'];
            $card_expiry = $_POST['card_expiry'];
            $card_cvv = $_POST['card_cvv'];
            $payment_details = "Card Number: $card_number, Expiry: $card_expiry, CVV: $card_cvv";
        } elseif ($payment_type === 'Online Banking') {
            $bank_name = $_POST['bank_name'];
            $account_number = $_POST['account_number'];
            $payment_details = "Bank: $bank_name, Account Number: $account_number";
        }

        // Prepare the SQL query (do not include 'paymentid' since it is auto-generated)
        $sql = "INSERT INTO payment (order_id, payment_date, payment_type, payment_status, total_payment, payment_details) 
                VALUES ($1, $2, $3, $4, $5, $6)";

        // Prepare the statement
        $result = pg_prepare($conn, "insert_payment", $sql);

        // Check if preparation was successful
        if ($result) {
            // Execute the query
            $result = pg_execute($conn, "insert_payment", array($order_id, $payment_date, $payment_type, $payment_status, $total_payment, $payment_details));

            // Check if the query executed successfully
            if ($result) {
                // Generate a receipt
                $receipt = "Payment Receipt\n";
                $receipt .= "Order ID: " . $order_id . "\n";
                $receipt .= "Payment Date: " . $payment_date . "\n";
                $receipt .= "Payment Type: " . $payment_type . "\n";
                $receipt .= "Payment Status: " . $payment_status . "\n";
                $receipt .= "Total Payment: RM " . number_format($total_payment, 2) . "\n";
                $receipt .= "Payment Details: " . $payment_details . "\n";

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
    }
} else {
    // Determine which step to display
    $step = isset($_GET['step']) ? $_GET['step'] : 1;

    if ($step == 1) {
        // Display order ID input form
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Payment Form</title>
            <link rel="stylesheet" href="style.css"> <!-- Link to your CSS file -->
        </head>
        <body>
            <div class="login-container">
                <h2 class="heading">Complete Your Payment</h2>
                <form method="POST" action="payment.php">
                    <label for="order_id">Order ID:</label>
                    <input type="number" name="order_id" id="order_id" required><br><br>
                    
                    <button type="submit">Next</button>
                </form>
            </div>
        </body>
        </html>
        <?php
    } elseif ($step == 2) {
        // Display payment type selection form
        $total_payment = $_SESSION['total_payment'];
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Select Payment Type</title>
            <link rel="stylesheet" href="style.css"> <!-- Link to your CSS file -->
        </head>
        <body>
            <div class="login-container">
                <h2 class="heading">Select Payment Type</h2>
                <p>Total Payment: RM <?php echo number_format($total_payment, 2); ?></p>
                <form method="POST" action="payment.php">
                    <label for="payment_type">Payment Type:</label>
                    <select name="payment_type" id="payment_type" required>
                        <option value="Cash">Cash</option>
                        <option value="Credit Card">Credit Card</option>
                        <option value="Online Banking">Online Banking</option>
                        <option value="Digital Wallet">Digital Wallet</option>
                    </select><br><br>
                    <button type="submit">Next</button>
                </form>
            </div>
        </body>
        </html>
        <?php
    } elseif ($step == 3) {
        // Display payment details form based on selected payment type
        $payment_type = $_SESSION['payment_type'];
        $total_payment = $_SESSION['total_payment'];
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Payment Details</title>
            <link rel="stylesheet" href="style.css"> <!-- Link to your CSS file -->
        </head>
        <body>
            <div class="login-container">
                <h2 class="heading">Enter Payment Details</h2>
                <p>Total Payment: RM <?php echo number_format($total_payment, 2); ?></p>
                <form method="POST" action="payment.php">
                    <?php if ($payment_type === 'Credit Card') { ?>
                        <label for="card_number">Card Number:</label>
                        <input type="text" name="card_number" id="card_number" required><br><br>
                        <label for="card_expiry">Expiry Date:</label>
                        <input type="text" name="card_expiry" id="card_expiry" required><br><br>
                        <label for="card_cvv">CVV:</label>
                        <input type="text" name="card_cvv" id="card_cvv" required><br><br>
                    <?php } elseif ($payment_type === 'Online Banking') { ?>
                        <label for="bank_name">Bank Name:</label>
                        <input type="text" name="bank_name" id="bank_name" required><br><br>
                        <label for="account_number">Account Number:</label>
                        <input type="text" name="account_number" id="account_number" required><br><br>
                    <?php } ?>
                    <button type="submit" name="payment_details">Submit Payment</button>
                </form>
            </div>
        </body>
        </html>
        <?php
    }
}
?>