<?php
require_once "config.php";

if (!isset($_GET['isbn'])) {
    die("Book not found");
}

$isbn = $_GET['isbn'];
$stmt = $pdo->prepare("SELECT * FROM BOOK WHERE Book_ISBN = :isbn");
$stmt->execute(['isbn' => $isbn]);
$book = $stmt->fetch();

if (!$book) {
    die("Book not found");
}

// Fetch category name
$stmt = $pdo->prepare("SELECT CategoryName FROM CATEGORY WHERE CategoryID = :categoryID");
$stmt->execute(['categoryID' => $book['CategoryID']]);
$category = $stmt->fetch();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Details - BOOKSPHERE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { 
            background-color: #f4edca; 
        }

        .book-details-container {
            margin: 150px auto;
            padding: 20px;
            max-width: 600px;
            border: 1px solid #ddd;
            border-radius: 10px;
            background-color: rgb(230, 223, 187);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .book-image {
            display: block;
            margin: 0 auto 20px;
            max-width: 200px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        /* New Styling for Text Below Title */
        .book-details-container p {
            font-size: 18px; /* Adjusted font size */
            line-height: 1.6; /* Better spacing */
            color: #333; /* Darker text color */
        }

        .book-details-container p strong {
            font-size: 20px; /* Bigger size for strong text */
            font-weight: bold;
            color: #000; /* Emphasize labels */
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="book-details-container">
            <!-- Book Image -->
            <?php if (!empty($book['ImagePath'])): ?>
                <img src="images/<?php echo htmlspecialchars($book['ImagePath']); ?>" alt="Book Image" class="book-image">
            <?php else: ?>
                <img src="images/default-book.jpg" alt="Default Book Image" class="book-image">
            <?php endif; ?>

            <!-- Book Details -->
            <h1 class="text-center"><?php echo htmlspecialchars($book['BookTitle']); ?></h1>
            <p><strong>Price:</strong> RM <?php echo number_format($book['BookPrice'], 2); ?></p>
            <p><strong>Stock:</strong> <?php echo $book['BookStock']; ?> available</p>
            <p><strong>Year:</strong> <?php echo $book['PublicationYear']; ?></p>
            <p><strong>Category:</strong> <?php echo htmlspecialchars($category['CategoryName']); ?></p>

            <!-- Quantity and Add to Cart -->
            <form action="add-to-cart.php" method="GET" class="text-center">
                <input type="hidden" name="isbn" value="<?php echo htmlspecialchars($isbn); ?>">
                <label for="quantity"><strong>Quantity:</strong></label>
                <input type="number" name="quantity" id="quantity" min="1" max="<?php echo $book['BookStock']; ?>" value="1" class="form-control w-50 d-inline">
                <button type="submit" class="btn btn-success mt-2">Add to Cart</button>
            </form>

            <!-- Back to Books -->
            <div class="text-center mt-4">
                <a href="books.php" class="btn btn-secondary">Back to Books</a>
            </div>
        </div>
    </div>
</body>
</html>