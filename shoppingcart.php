<?php
session_start();
require_once 'mieraconnect.php'; // Include your database configuration file

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
function calculateTotal($pdo) {
    $total = 0;
    foreach ($_SESSION['cart'] as $bookISBN => $quantity) {
        $query = 'SELECT "BookPrice" FROM "book" WHERE "Book_ISBN" = :isbn';
        $stmt = $pdo->prepare($query);
        $stmt->execute(['isbn' => $bookISBN]);
        $book = $stmt->fetch();

        if ($book) {
            $total += $book['BookPrice'] * $quantity;
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
        $totalPrice = calculateTotal($pdo);

        // Create order
        $query = 'INSERT INTO "order" ("Order_Date", "Total_Price", "CustomerID") VALUES (NOW(), :total_price, :customer_id) RETURNING "Order_ID"';
        $stmt = $pdo->prepare($query);
        $stmt->execute(['total_price' => $totalPrice, 'customer_id' => $customerID]);
        
        $orderID = $stmt->fetchColumn();

        // Insert order items
        foreach ($_SESSION['cart'] as $bookISBN => $quantity) {
            $query = 'INSERT INTO "orderlist" ("Book_ISBN", "Order_ID", "Quantity") VALUES (:isbn, :order_id, :quantity)';
            $stmt = $pdo->prepare($query);
            $stmt->execute(['isbn' => $bookISBN, 'order_id' => $orderID, 'quantity' => $quantity]);
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
    <link rel="stylesheet" href="style.css"> <!-- Link to your CSS file -->
</head>
<body>
    <div class="login-container"> <!-- Use the same container class for consistency -->
        <h2 class="heading">Shopping Cart</h2>

        <!-- Book List -->
        <h2 class="heading">Books</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>ISBN</th>
                    <th>Title</th>
                    <th>Price</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $query = 'SELECT "Book_ISBN", "BookTitle", "BookPrice" FROM "book"';
                $stmt = $pdo->query($query);

                while ($row = $stmt->fetch()) {
                    echo "<tr>";
                    echo "<td>{$row['Book_ISBN']}</td>";
                    echo "<td>{$row['BookTitle']}</td>";
                    echo "<td>{$row['BookPrice']}</td>";
                    echo '<td>
                        <form method="POST" class="form-inline">
                            <input type="hidden" name="book_isbn" value="' . $row['Book_ISBN'] . '">
                            <input type="number" name="quantity" value="1" min="1" class="quantity-input">
                            <button type="submit" name="add_to_cart" class="btn">Add to Cart</button>
                        </form>
                    </td>';
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>

        <!-- Shopping Cart -->
        <h2 class="heading">Your Cart</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>ISBN</th>
                    <th>Quantity</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($_SESSION['cart'] as $bookISBN => $quantity) {
                    echo "<tr>";
                    echo "<td>$bookISBN</td>";
                    echo "<td>$quantity</td>";
                    echo '<td>
                        <form method="POST" class="form-inline" style="display:inline;">
                            <input type="hidden" name="book_isbn" value="' . $bookISBN . '">
                            <input type="number" name="quantity" value="' . $quantity . '" min="0" class="quantity-input">
                            <button type="submit" name="update_cart" class="btn">Update</button>
                        </form>
                        <form method="POST" class="form-inline" style="display:inline;">
                            <input type="hidden" name="book_isbn" value="' . $bookISBN . '">
                            <button type="submit" name="remove_from_cart" class="btn">Remove</button>
                        </form>
                    </td>';
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>

        <!-- Checkout -->
        <h3 class="total">Total: <?php echo calculateTotal($pdo); ?></h3>
        <form method="POST" class="form-checkout">
            <button type="submit" name="checkout" class="btn-checkout">Checkout</button>
        </form>
    </div>
</body>
</html>