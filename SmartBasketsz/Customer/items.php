<?php
include_once "../config.php";
session_start();

if (!isset($_SESSION["email"])) {
    header("Location: login.php");
    exit();
}

// Initialize variables for category filtering
$selectedCategory = "";
$filterClause = "";

// Check if the category filter has been submitted via GET
if (isset($_GET['category']) && $_GET['category'] !== "") {
    $selectedCategory = $_GET['category'];
    // Sanitize the input using real_escape_string
    $selectedCategoryEscaped = $conn->real_escape_string($selectedCategory);
    $filterClause = " WHERE category = '$selectedCategoryEscaped' ";
}

$sql = "SELECT * FROM Item" . $filterClause;
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Items Page</title>
    <link rel="icon" type="image/x-icon" href="../SmartBasketszLogo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC"
          crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <link rel="stylesheet" type="text/css" href="../template.css?v=202505191827">
</head>
<body>
<!-- Logo -->
<div class="container-fluid d-flex justify-content-center align-items-center header-bar">
    <header>
        <img src="../SmartBasketszLogo.png" height="100" width="100">
    </header>
</div>
<!-- Navigation Bar -->
<nav class="theNavBar nav nav-pills flex-column flex-sm-row">
    <a class="flex-sm-fill text-sm-center nav-link" href="home.php">Home</a>
    <a class="flex-sm-fill text-sm-center nav-link" href="profile.php">Profile</a>
    <a class="flex-sm-fill text-sm-center nav-link active" href="items.php">Items</a>
    <a class="flex-sm-fill text-sm-center nav-link" href="cart.php">Cart</a>
    <a class="flex-sm-fill text-sm-center nav-link" href="contactForm.php">Contact Form</a>
    <a class="flex-sm-fill text-sm-center nav-link" href="logout.php">Logout</a>
</nav>
<div class="container mt-4">
    <!-- Category Filter Form -->
    <form method="GET" action="items.php" class="mb-3">
        <label for="category">Filter by category:</label>
        <select name="category" id="category" class="form-select w-auto d-inline-block">
            <!-- The "All" option has an empty value -->
            <option value="" <?php echo ($selectedCategory === "") ? "selected" : ""; ?>>All</option>
            <option value="Carbohydrate" <?php echo ($selectedCategory === "Carbohydrate") ? "selected" : ""; ?>>Carbohydrate</option>
            <option value="Vegetable" <?php echo ($selectedCategory === "Vegetable") ? "selected" : ""; ?>>Vegetable</option>
            <option value="Fruit" <?php echo ($selectedCategory === "Fruit") ? "selected" : ""; ?>>Fruit</option>
            <option value="Beverage" <?php echo ($selectedCategory === "Beverage") ? "selected" : ""; ?>>Beverage</option>
            <!-- Add more options as needed -->
        </select>
        <button type="submit" class="btn btn-primary">Filter</button>
    </form>

    <!-- Items Table -->
    <table class="table table-striped table-success">
        <thead>
        <tr>
            <th colspan="9" style="text-align: center; font-size: 30px;">Items</th>
        </tr>
        <tr>
            <th>Index</th>
            <th>ID</th>
            <th>Name</th>
            <th>Description</th>
            <th>Category</th>
            <th>Sale Price (RM)</th>
            <th>Image</th>
            <th>Order</th>
        </tr>
        </thead>
        <tbody>
        <?php
        if ($result && $result->num_rows > 0) {
            $index = 0; // Initialize index counter
            while ($row = $result->fetch_assoc()) {
                $index++;
                ?>
                <tr>
                    <td><?php echo $index; ?></td>
                    <td><?php echo $row['ID']; ?></td>
                    <td><?php echo $row['name']; ?></td>
                    <td><?php echo $row['description']; ?></td>
                    <td><?php echo $row['category']; ?></td>
                    <td><?php echo $row['salePrice']; ?></td>
                    <td><img src="../ItemImages/<?php echo $row['image']; ?>" alt="Item Image" height="50px" width="50px"></td>
                    <td>
                        <!-- Order Form for the current item -->
                        <form action="addItemToCart.php" method="post">
                            <input type="hidden" name="ID" value="<?php echo $row['ID']; ?>">
                            <div class="mb-2">
                                <input type="number" name="quantity" min="1" value="1" class="form-control" required>
                            </div>
                            <button type="submit" class="btn btn-success">Add to Order</button>
                        </form>
                    </td>
                </tr>
                <?php
            }
        } else {
            echo "<tr><td colspan='8'>No items found</td></tr>";
        }
        ?>
        </tbody>
    </table>
</div>
<!-- Footer -->
<div class="container-fluid d-flex justify-content-center align-items-center footer-bar">
    <footer>
        <p>&copy; 2025 - SmartBasketsz</p>
        <address><i>Management and Science University, University Drive, Off Persiaran Sukan, Section 13, 40100 Shah Alam Selangor Malaysia</i></address>
    </footer>
</div>
</body>
</html>