<?php
session_start();

// Database connection
$host = 'localhost';
$username = 'postgres';
$password = 'miera1234'; // Replace with your DB password
$dbname = 'onlinebookspheresystem'; // Replace with your DB name

// Create connection string
$conn = pg_connect("host=$host dbname=$dbname user=$username password=$password");

// Check connection
if (!$conn) {
    die("Connection failed: " . pg_last_error());
}

// Initialize shopping cart if not already done
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Function to add a book to the cart
function addToCart($bookISBN, $quantity) {
    if (isset($_SESSION['cart'][$bookISBN])) {
        $_SESSION['cart'][$bookISBN] += $quantity;
    } else {
        $_SESSION['cart'][$bookISBN] = $quantity;
    }
}

// Function to remove a book from the cart
function removeFromCart($bookISBN) {
    unset($_SESSION['cart'][$bookISBN]);
}

// Function to update the quantity of a book in the cart
function updateCart($bookISBN, $quantity) {
    if ($quantity <= 0) {
        removeFromCart($bookISBN);
    } else {
        $_SESSION['cart'][$bookISBN] = $quantity;
    }
}

// Function to calculate the total price of the cart
function calculateTotal($conn) {
    $total = 0;
    foreach ($_SESSION['cart'] as $bookISBN => $quantity) {
        $query = "SELECT BookPrice FROM BOOK WHERE Book_ISBN = $1";
        $result = pg_query_params($conn, $query, [$bookISBN]);

        if ($result && pg_num_rows($result) > 0) {
            $book = pg_fetch_assoc($result);
            $total += $book['bookprice'] * $quantity;
        }
    }
    return $total;
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_to_cart'])) {
        addToCart($_POST['book_isbn'], (int)$_POST['quantity']);
    } elseif (isset($_POST['remove_from_cart'])) {
        removeFromCart($_POST['book_isbn']);
    } elseif (isset($_POST['update_cart'])) {
        updateCart($_POST['book_isbn'], (int)$_POST['quantity']);
    } elseif (isset($_POST['checkout'])) {
        // Handle checkout process
        $customerID = 1; // Replace with logged-in customer ID
        $totalPrice = calculateTotal($conn);

        // Create order
        $query = "INSERT INTO \"ORDER\" (Order_Date, Total_Price, CustomerID) VALUES (NOW(), $1, $2) RETURNING Order_ID";
        $result = pg_query_params($conn, $query, [$totalPrice, $customerID]);

        if (!$result) {
            die("Failed to insert order: " . pg_last_error($conn));
        }

        // Fetch the generated Order_ID
        $orderID = pg_fetch_result($result, 0, 'order_id');

        // Insert order items
        foreach ($_SESSION['cart'] as $bookISBN => $quantity) {
            $query = "INSERT INTO ORDERLIST (Book_ISBN, Order_ID, Quantity) VALUES ($1, $2, $3)";
            pg_query_params($conn, $query, [$bookISBN, $orderID, $quantity]);
        }

        // Clear cart after checkout
        $_SESSION['cart'] = [];
        $_SESSION['order_id'] = $orderID; // Save order ID for shipping

        echo "<script>alert('Order placed successfully!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart</title>
</head>
<body>
    <h1>Shopping Cart</h1>

    <!-- Book List -->
    <h2>Books</h2>
    <table border="1">
        <tr>
            <th>ISBN</th>
            <th>Title</th>
            <th>Price</th>
            <th>Action</th>
        </tr>
        <?php
        $query = "SELECT Book_ISBN, BookTitle, BookPrice FROM BOOK";
        $result = pg_query($conn, $query);

        while ($row = pg_fetch_assoc($result)) {
            echo "<tr>";
            echo "<td>{$row['book_isbn']}</td>";
            echo "<td>{$row['booktitle']}</td>";
            echo "<td>{$row['bookprice']}</td>";
            echo "<td>
                <form method='POST'>
                    <input type='hidden' name='book_isbn' value='{$row['book_isbn']}'>
                    <input type='number' name='quantity' value='1' min='1'>
                    <button type='submit' name='add_to_cart'>Add to Cart</button>
                </form>
            </td>";
            echo "</tr>";
        }
        ?>
    </table>

    <!-- Shopping Cart -->
    <h2>Your Cart</h2>
    <table border="1">
        <tr>
            <th>ISBN</th>
            <th>Quantity</th>
            <th>Action</th>
        </tr>
        <?php
        foreach ($_SESSION['cart'] as $bookISBN => $quantity) {
            echo "<tr>";
            echo "<td>$bookISBN</td>";
            echo "<td>$quantity</td>";
            echo "<td>
                <form method='POST' style='display:inline;'>
                    <input type='hidden' name='book_isbn' value='$bookISBN'>
                    <input type='number' name='quantity' value='$quantity' min='0'>
                    <button type='submit' name='update_cart'>Update</button>
                </form>
                <form method='POST' style='display:inline;'>
                    <input type='hidden' name='book_isbn' value='$bookISBN'>
                    <button type='submit' name='remove_from_cart'>Remove</button>
                </form>
            </td>";
            echo "</tr>";
        }
        ?>
    </table>

    <!-- Checkout -->
    <h3>Total: <?php echo calculateTotal($conn); ?></h3>
    <form method="POST">
        <button type="submit" name="checkout">Checkout</button>
    </form>
</body>
</html>
