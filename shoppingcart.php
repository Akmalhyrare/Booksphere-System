<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link to your CSS file -->
</head>
<body>
    <div class="container">
        <h1 class="header">Shopping Cart</h1>

        <!-- Book List -->
        <h2 class="sub-header">Books</h2>
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
                $query = "SELECT Book_ISBN, BookTitle, BookPrice FROM BOOK";
                $result = pg_query($conn, $query);

                while ($row = pg_fetch_assoc($result)) {
                    echo "<tr>";
                    echo "<td>{$row['book_isbn']}</td>";
                    echo "<td>{$row['booktitle']}</td>";
                    echo "<td>{$row['bookprice']}</td>";
                    echo "<td>
                        <form method='POST' class='form-inline'>
                            <input type='hidden' name='book_isbn' value='{$row['book_isbn']}'>
                            <input type='number' name='quantity' value='1' min='1' class='quantity-input'>
                            <button type='submit' name='add_to_cart' class='btn'>Add to Cart</button>
                        </form>
                    </td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>

        <!-- Shopping Cart -->
        <h2 class="sub-header">Your Cart</h2>
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
                    echo "<td>
                        <form method='POST' class='form-inline' style='display:inline;'>
                            <input type='hidden' name='book_isbn' value='$bookISBN'>
                            <input type='number' name='quantity' value='$quantity' min='0' class='quantity-input'>
                            <button type='submit' name='update_cart' class='btn'>Update</button>
                        </form>
                        <form method='POST' class='form-inline' style='display:inline;'>
                            <input type='hidden' name='book_isbn' value='$bookISBN'>
                            <button type='submit' name='remove_from_cart' class='btn'>Remove</button>
                        </form>
                    </td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>

        <!-- Checkout -->
        <h3 class="total">Total: <?php echo calculateTotal($conn); ?></h3>
        <form method="POST" class="form-checkout">
            <button type="submit" name="checkout" class="btn-checkout">Checkout</button>
        </form>
    </div>
</body>
</html>