<?php
session_start();
require_once "config.php";

// Check if the user is logged in, otherwise redirect to login page
if (!isset($_SESSION['CustomerID'])) {
    header('Location: login.php');
    exit;
}

// Fetch logged-in user information
$customerID = $_SESSION['CustomerID'];
$stmt = $pdo->prepare("SELECT CustUsername, CustEmail FROM CUSTOMER WHERE CustomerID = :customerID");
$stmt->execute(['customerID' => $customerID]);
$user = $stmt->fetch();
$username = $user['CustUsername'];
$email = $user['CustEmail'];

// Fetch all categories for the dropdown
$stmt = $pdo->query("SELECT * FROM CATEGORY");
$categories = $stmt->fetchAll();

// Fetch books based on selected category or all books
if (isset($_GET['category']) && !empty($_GET['category'])) {
    $category = $_GET['category'];
    $stmt = $pdo->prepare("
        SELECT BOOK.*, CATEGORY.CategoryName 
        FROM BOOK 
        JOIN CATEGORY ON BOOK.CategoryID = CATEGORY.CategoryID 
        WHERE CATEGORY.CategoryName = :category
    ");
    $stmt->execute(['category' => $category]);
} else {
    $stmt = $pdo->query("
        SELECT BOOK.*, CATEGORY.CategoryName 
        FROM BOOK 
        JOIN CATEGORY ON BOOK.CategoryID = CATEGORY.CategoryID
    ");
}
$books = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Books - BOOKSPHERE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f4edca; }
        header { display: flex; justify-content: space-between; align-items: center; padding: 20px; background-color: #ffe599; position: fixed; top: 0; width: 100%; z-index: 1000; }
        header nav a { color: #ce7e00; text-decoration: none; margin-right: 30px; font-size: 24px; }

        .profile-menu { position: relative; display: inline-block; }
        .profile-btn { 
            background-color: #ce7e00;
            color: white;
            border: none;
            padding: 8px 25px;
            cursor: pointer;
            border-radius: 35px;
            font-size: 23px; 
            margin-right: 10px; 
        }
        .dropdown-content { 
            display: none; 
            position: absolute; 
            background-color: rgb(237, 230, 194); 
            right: 0; 
            min-width: 250px; 
            border-radius: 20px; 
            padding: 12px; 
            font-size: 23px; 
        }
        .profile-menu:hover .dropdown-content { display: block; }

        .dropdown-content p, .dropdown-content a { 
            margin: 5px 0; 
            color: #333; 
            font-size: 22px; 
        }
        .dropdown-content a { 
            text-decoration: none; 
            color: #ce7e00; 
        }
        .dropdown-content a:hover { text-decoration: underline; }

        .main-content { margin-top: 150px; }
        .form-select, .btn-warning { font-size: 20px; }
    </style>
</head>
<body>
    <header>
        <h1 style="color: #ce7e00;">Booksphere</h1>

        <!-- Search by Category Form -->
        <form action="books.php" method="GET" style="flex-grow: 1; text-align: center;">
            <select name="category" class="form-select d-inline w-auto">
                <option value="">-- Select Category --</option>
                <?php foreach ($categories as $categoryOption): ?>
                    <option value="<?php echo htmlspecialchars($categoryOption['CategoryName']); ?>"
                        <?php if (isset($_GET['category']) && $_GET['category'] === $categoryOption['CategoryName']) echo 'selected'; ?>>
                        <?php echo htmlspecialchars($categoryOption['CategoryName']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="btn btn-warning">Search</button>
        </form>

        <nav>
            <a href="dashboard.php">Home</a>
            <a href="books.php">All Books</a>
            <a href="cart.php">Cart</a>
            <div class="profile-menu">
                <button class="profile-btn">Profile</button>
                <div class="dropdown-content">
                    <p>Username: <?php echo htmlspecialchars($username); ?></p>
                    <p>Email: <?php echo htmlspecialchars($email); ?></p>
                    <a href="profile.php">View Profile</a>
                    <a href="logout.php" style="color: red;">Logout</a>
                </div>
            </div>
        </nav>
    </header>

    <div class="container main-content">
        <h1 class="text-center text-primary">Books Available</h1>
        <?php if (isset($_GET['category']) && !empty($_GET['category'])): ?>
            <p class="text-center">Showing results for category: <strong><?php echo htmlspecialchars($_GET['category']); ?></strong></p>
        <?php endif; ?>
        <div class="row">
            <?php if (empty($books)): ?>
                <p class="text-center">No books found for the selected category.</p>
            <?php else: ?>
                <?php foreach ($books as $book): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card">
                            <img src="images/<?php echo htmlspecialchars($book['ImagePath']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($book['BookTitle']); ?>">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($book['BookTitle']); ?></h5>
                                <p class="card-text">
                                    <strong>Category:</strong> <?php echo htmlspecialchars($book['CategoryName']); ?><br>
                                    <strong>Price:</strong> RM <?php echo number_format($book['BookPrice'], 2); ?><br>
                                    <strong>Year:</strong> <?php echo htmlspecialchars($book['PublicationYear']); ?><br>
                                    <strong>Stock:</strong> <?php echo htmlspecialchars($book['BookStock']); ?>
                                </p>
                                <a href="book-details.php?isbn=<?php echo $book['Book_ISBN']; ?>" class="btn btn-primary">View Details</a>
                                <a href="add-to-cart.php?isbn=<?php echo $book['Book_ISBN']; ?>" class="btn btn-success">Add to Cart</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
