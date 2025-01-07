<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Form</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link to your CSS file -->
</head>
<body>
    <div class="payment-container">
        <h2 class="heading">Complete Your Payment</h2>
        <form method="POST" action="payment.php" class="payment-form">
            <div class="form-group">
                <label for="order_id">Order ID:</label>
                <input type="number" name="order_id" id="order_id" required>
            </div>
            
            <div class="form-group">
                <label for="payment_type">Payment Type:</label>
                <select name="payment_type" id="payment_type" required>
                    <option value="Cash">Cash</option>
                    <option value="Credit Card">Credit Card</option>
                    <option value="Online Banking">Online Banking</option>
                    <option value="Digital Wallet">Digital Wallet</option>
                </select>
            </div>
    
            
            <div class="form-group">
                <label for="total_payment">Total Payment (RM):</label>
                <input type="number" name="total_payment" id="total_payment" step="0.01" required>
            </div>
            
            <button type="submit" class="submit-button">Submit Payment</button>
        </form>
    </div>
</body>
</html>
<?php
?>