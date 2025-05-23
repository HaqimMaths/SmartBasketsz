<?php
include_once "../config.php";
session_start();

if (!isset($_SESSION["email"])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Past Carts</title>
    <link rel="icon" type="image/x-icon" href="../SmartBasketszLogo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
            crossorigin="anonymous"></script>
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
    <a class="flex-sm-fill text-sm-center nav-link" href="items.php">Items</a>
    <a class="flex-sm-fill text-sm-center nav-link active" href="cart.php">Cart</a>
    <a class="flex-sm-fill text-sm-center nav-link" href="contactForm.php">Contact Form</a>
    <a class="flex-sm-fill text-sm-center nav-link" href="logout.php">Logout</a>
</nav>
<div class="container mt-4">
    <table class="table table-striped table-success">
        <thead>
        <tr>
            <!-- Update colspan to match the new total number of columns -->
            <th colspan="8" style="text-align: center; font-size: 30px;">Past Carts</th>
        </tr>
        <tr>
            <th>Index</th>
            <th>ID</th>
            <th>Order Date</th>
            <th>Items</th>
            <th>Total Price (RM)</th>
            <th>Payment Method</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $sql = "SELECT 
            c.ID,
            c.orderDate,
            c.paymentMethod,
            GROUP_CONCAT(CONCAT(i.name, ' (Qty: ', oi.quantity, ')') SEPARATOR ', ') AS Items,
            SUM(i.salePrice * oi.quantity) AS TotalPrice
            FROM cart AS c
            JOIN orderItem AS oi ON c.ID = oi.cartId
            JOIN item AS i ON oi.itemId = i.ID
            WHERE c.customerId = '{$_SESSION['ID']}'
            GROUP BY c.ID
            ORDER BY c.orderDate DESC";

        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            $index = 0; // Initialize the index counter
            while($row = $result->fetch_assoc()){
                $index++;
                echo "<tr>
                      <td>$index</td>
                      <td>{$row['ID']}</td>
                      <td>{$row['orderDate']}</td>
                      <td>{$row['Items']}</td>
                      <td>{$row['TotalPrice']}</td>
                      <td>{$row['paymentMethod']}</td>
                      </tr>";
            }
        } else {
            echo "<tr><td colspan='6'>No carts found</td></tr>";
        }
        ?>
        </tbody>
    </table>
    <a href="cart.php" class="btn btn-success">Back</a>
</div>
<!-- Footer -->
<div class="container-fluid d-flex justify-content-center align-items-center footer-bar">
    <footer>
        <p>&copy; 2025 - SmartBasketsz</p>
        <address><i>Management and Science University, University Drive, Off Persiaran Sukan, Section 13 40100 Shah Alam Selangor Malaysia</i></address>
    </footer>
</div>
</body>
</html>