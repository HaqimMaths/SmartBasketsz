<?php
include_once "../config.php";
session_start();
if (!isset($_SESSION["email"])) {
    header("Location: login.php");
    exit();
}
// --- Queries ---
// Adjust these queries based on your actual table and column names.
$customersQuery  = "SELECT * FROM Customer";
$adminsQuery = "SELECT * FROM Admin";

$customersResult  = $conn->query($customersQuery);
$adminsResult = $conn->query($adminsQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Users List</title>
    <link rel="icon" type="image/x-icon" href="../SmartBasketszLogo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
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
<nav class="nav nav-pills flex-column flex-sm-row theNavBar">
    <a class="flex-sm-fill text-sm-center nav-link" href="dashboard.php">Dashboard</a>
    <a class="flex-sm-fill text-sm-center nav-link" href="profile.php">Profile</a>
    <a class="flex-sm-fill text-sm-center nav-link" href="items.php">Items</a>
    <a class="flex-sm-fill text-sm-center nav-link" href="carts.php">Carts</a>
    <a class="flex-sm-fill text-sm-center nav-link" href="messageViewer.php">Message Viewer</a>
    <a class="flex-sm-fill text-sm-center nav-link active" href="users.php">Users</a>
    <a class="flex-sm-fill text-sm-center nav-link" href="logout.php">Logout</a>
</nav>
<!-- Users and Admins Tables -->
<div class="container mt-5">
    <!-- Users Table -->
    <table class="table table-striped table-success">
        <thead>
            <tr>
                <th colspan="7" style="text-align: center"><h2><b>Customers</b></h2></th>
            </tr>
        </thead>
        <tbody
            <tr>
                <th>ID</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Address</th>
                <th>Country</th>
                <th>Email</th>
                <th>Phone Number</th>
            </tr>
        </thead>
        <tbody>
            <?php if($customersResult && $customersResult->num_rows > 0): ?>
                <?php while($customer = $customersResult->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($customer['ID']); ?></td>
                        <td><?php echo htmlspecialchars($customer['firstName']); ?></td>
                        <td><?php echo htmlspecialchars($customer['lastName']); ?></td>
                        <td><?php echo htmlspecialchars($customer['address']); ?></td>
                        <td><?php echo htmlspecialchars($customer['country']); ?></td>
                        <td><?php echo htmlspecialchars($customer['email']); ?></td>
                        <td><?php echo htmlspecialchars($customer['phoneNumber']); ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7">No customers found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    <hr>
    <!-- Admins Table -->
    <table class="table table-striped table-success">
        <thead>
            <tr>
                <th colspan="7" style="text-align: center"><h2><b>Admins</b></h2></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <th>ID</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Address</th>
                <th>Country</th>
                <th>Email</th>
                <th>Phone Number</th>
            </tr>
            <?php if($adminsResult && $adminsResult->num_rows > 0): ?>
                <?php while($admin = $adminsResult->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($admin['ID']); ?></td>
                        <td><?php echo htmlspecialchars($admin['firstName']); ?></td>
                        <td><?php echo htmlspecialchars($admin['lastName']); ?></td>
                        <td><?php echo htmlspecialchars($admin['address']); ?></td>
                        <td><?php echo htmlspecialchars($admin['country']); ?></td>
                        <td><?php echo htmlspecialchars($admin['email']); ?></td>
                        <td><?php echo htmlspecialchars($admin['phoneNumber']); ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7">No admins found.</td>
                </tr>
            <?php endif; ?>
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