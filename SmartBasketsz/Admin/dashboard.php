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
    <title>Admin Dashboard</title>
    <link rel="icon" type="image/x-icon" href="../SmartBasketszLogo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <script src="https://cdn.canvasjs.com/canvasjs.min.js"></script>
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
<nav class="nav nav-pills flex-column flex-sm-row theNavBar">
    <a class="flex-sm-fill text-sm-center nav-link active" href="dashboard.php">Dashboard</a>
    <a class="flex-sm-fill text-sm-center nav-link" href="profile.php">Profile</a>
    <a class="flex-sm-fill text-sm-center nav-link" href="items.php">Items</a>
    <a class="flex-sm-fill text-sm-center nav-link" href="carts.php">Carts</a>
    <a class="flex-sm-fill text-sm-center nav-link" href="messageViewer.php">Message Viewer</a>
    <a class="flex-sm-fill text-sm-center nav-link" href="users.php">Users</a>
    <a class="flex-sm-fill text-sm-center nav-link" href="logout.php">Logout</a>
</nav>
<div class="container mt-4">
    <table class="table table-striped table-success">
        <thead>
        <tr>
            <th colspan="6" style="text-align: center; font-size: 30px;">Statistics</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <th>Items Sold</th>
            <th>Total Sales (RM)</th>
            <th>Total Items</th>
            <th>Total Customers</th>
            <th>Total Orders</th>
            <th>Total Messages</th>
        </tr>
        <?php
        $sql = "SELECT 
                        (SELECT SUM(orderItem.quantity) FROM orderItem) AS totalItemsSold,
                        (SELECT SUM(cart.totalPrice) FROM cart) AS totalSales,
                        (SELECT COUNT(*) FROM item) AS totalItemsInStock,
                        (SELECT COUNT(*) FROM customer) AS totalCustomers,
                        (SELECT COUNT(*) FROM cart) AS totalCarts,
                        (SELECT COUNT(*) FROM contactForm) AS totalMessages";


        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()){
                echo "<tr>
                      <td>{$row['totalItemsSold']}</td>
                      <td>{$row['totalSales']}</td>
                      <td>{$row['totalItemsInStock']}</td>
                      <td>{$row['totalCustomers']}</td>
                      <td>{$row['totalCarts']}</td>
                      <td>{$row['totalMessages']}</td>
                      </tr>";
            }
        } else {
            echo "<tr><td colspan='8'>No data found</td></tr>";
        }
        ?>
        </tbody>
    </table>
</div>
<div class="container-fluid d-flex justify-content-center align-items-center footer-bar">
    <footer>
        <p>&copy; 2025 - SmartBasketsz</p>
        <address><i>Management and Science University, University Drive, Off Persiaran Sukan, Section 13 40100 Shah Alam Selangor Malaysia</i></address>
    </footer>
</div>
</body>
</html>
