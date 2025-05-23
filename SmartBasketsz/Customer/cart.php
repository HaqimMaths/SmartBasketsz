<?php
include_once "../config.php";
session_start();
if (!isset($_SESSION["email"])) {
    header("Location: login.php");
    exit();
}

// --- POST HANDLING ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST['action'] ?? '';
    if ($action == "update_payment") {
        $cartId = intval($_POST["cartId"]);
        $paymentMethod = $conn->real_escape_string($_POST["paymentMethod"] ?? '');
        if (empty($paymentMethod))
            $_SESSION["error"] = "Select a payment method.";
        else {
            $sql = "UPDATE cart SET paymentMethod='$paymentMethod' WHERE ID=$cartId";
            $_SESSION["message"] = ($conn->query($sql) === TRUE)
                ? "Payment updated!" : "Error: " . $conn->error;
        }
    }
    if ($action == "decrease") {
        $orderItemId = intval($_POST["orderItemId"]);
        $cartId = intval($_POST["cartId"]);
        $res = $conn->query("SELECT quantity FROM orderItem WHERE ID=$orderItemId");
        if ($res && $res->num_rows > 0) {
            $row = $res->fetch_assoc();
            $newQty = $row["quantity"] - 1;
            if ($newQty <= 0)
                $conn->query("DELETE FROM orderItem WHERE ID=$orderItemId");
            else
                $conn->query("UPDATE orderItem SET quantity=$newQty WHERE ID=$orderItemId");
            $resTotal = $conn->query("SELECT COALESCE(SUM(i.salePrice * oi.quantity),0) AS total 
                                       FROM orderItem oi 
                                       JOIN item i ON oi.itemId=i.ID 
                                       WHERE oi.cartId=$cartId");
            if($resTotal){
                $rowTotal = $resTotal->fetch_assoc();
                $total = $rowTotal["total"];
                $conn->query("UPDATE cart SET totalPrice=$total WHERE ID=$cartId");
            }
        }
    }
    if ($action == "checkout") {
        $cartId = intval($_POST["cartId"]);
        // (Optional: you might update the cart status here as 'completed')
        unset($_SESSION["cart_id"]); // Clear active cart.
        $_SESSION["message"] = "Checkout successful!";
        header("Location: cart.php");
        exit();
    }
    header("Location: cart.php");
    exit();
}

// --- NORMAL CART VIEW ---
if (!isset($_SESSION["cart_id"])) {
    $message = "Your shopping cart is empty.";
} else {
    $cartId = intval($_SESSION["cart_id"]);
    $resCart = $conn->query("SELECT * FROM cart WHERE ID=$cartId");
    if ($resCart && $resCart->num_rows > 0) {
        $cart = $resCart->fetch_assoc();
        $resItems = $conn->query("SELECT oi.ID as orderItemId, oi.quantity, i.name, i.salePrice, i.image 
                                  FROM orderItem oi 
                                  JOIN item i ON oi.itemId=i.ID 
                                  WHERE oi.cartId=$cartId");
    } else {
        $message = "No active cart found.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your Shopping Cart</title>
    <link rel="icon" href="../SmartBasketszLogo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
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
    <a class="flex-sm-fill text-sm-center nav-link" href="items.php">Items</a>
    <a class="flex-sm-fill text-sm-center nav-link active" href="cart.php">Cart</a>
    <a class="flex-sm-fill text-sm-center nav-link" href="contactForm.php">Contact Form</a>
    <a class="flex-sm-fill text-sm-center nav-link" href="logout.php">Logout</a>
</nav>
<div class="container mt-4">
    <h1>Your Shopping Cart</h1>
    <?php
    if (isset($_SESSION["error"])) {
        echo '<div class="alert alert-danger">' . $_SESSION["error"] . '</div>';
        unset($_SESSION["error"]);
    }
    if (isset($_SESSION["message"])) {
        echo '<div class="alert alert-success">' . $_SESSION["message"] . '</div>';
        unset($_SESSION["message"]);
    }
    if (isset($message)) { echo '<div class="alert alert-info">' . $message . '</div>'; }
    ?>
    <?php if (!isset($message)) { ?>
        <div class="card mb-3">
            <div class="card-header"><strong>Cart Summary</strong></div>
            <div class="card-body">
                <p><strong>Cart ID:</strong> <?php echo $cart["ID"]; ?></p>
                <p><strong>Order Date:</strong> <?php echo $cart["orderDate"]; ?></p>
                <p><strong>Payment Method:</strong> <?php echo $cart["paymentMethod"]; ?></p>
                <p><strong>Total Price:</strong> RM <?php echo number_format($cart["totalPrice"], 2); ?></p>
            </div>
        </div>
        <div class="card mb-3">
            <div class="card-header"><strong>Update Payment Method</strong></div>
            <div class="card-body">
                <form method="POST" action="cart.php">
                    <input type="hidden" name="action" value="update_payment">
                    <input type="hidden" name="cartId" value="<?php echo $cart["ID"]; ?>">
                    <div class="mb-3">
                        <label for="paymentMethod" class="form-label">Choose Payment Method:</label>
                        <select name="paymentMethod" id="paymentMethod" class="form-select" required>
                            <option value="">Select Payment Method</option>
                            <option value="Credit/Debit Card" <?php echo ($cart["paymentMethod"]=="Credit/Debit Card") ? "selected" : ""; ?>>Credit/Debit Card</option>
                            <option value="E-Wallet" <?php echo ($cart["paymentMethod"]=="E-Wallet") ? "selected" : ""; ?>>E-Wallet</option>
                            <option value="QR Pay" <?php echo ($cart["paymentMethod"]=="QR Pay") ? "selected" : ""; ?>>QR Pay</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-secondary mb-2">Update Payment Method</button>
                </form>
            </div>
        </div>
        <table class="table table-bordered table-striped">
            <thead>
            <tr>
                <th>#</th>
                <th>Item Name</th>
                <th>Sale Price (RM)</th>
                <th>Quantity</th>
                <th>Line Total (RM)</th>
                <th>Image</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody>
            <?php
            if ($resItems && $resItems->num_rows > 0) {
                $i = 1;
                while ($row = $resItems->fetch_assoc()) {
                    $lineTotal = $row["salePrice"] * $row["quantity"];
                    echo "<tr>
                      <td>{$i}</td>
                      <td>{$row['name']}</td>
                      <td>" . number_format($row['salePrice'], 2) . "</td>
                      <td>{$row['quantity']}</td>
                      <td>" . number_format($lineTotal, 2) . "</td>
                      <td>" . (!empty($row['image']) ? "<img src='../ItemImages/{$row['image']}' alt='Item Image' height='50' width='50'>" : "No image") . "</td>
                      <td>
                        <form method='POST' action='cart.php' style='display:inline;'>
                          <input type='hidden' name='action' value='decrease'>
                          <input type='hidden' name='orderItemId' value='{$row['orderItemId']}'>
                          <input type='hidden' name='cartId' value='{$cart["ID"]}'>
                          <button type='submit' class='btn btn-warning btn-sm'>-</button>
                        </form>
                      </td>
                    </tr>";
                    $i++;
                }
            } else {
                echo "<tr><td colspan='7'>Your cart is empty.</td></tr>";
            }
            ?>
            </tbody>
        </table>
        <div class="mt-3">
            <form method="POST" action="cart.php" onsubmit="return confirm('Checkout? This will clear your cart.')">
                <input type="hidden" name="action" value="checkout">
                <input type="hidden" name="cartId" value="<?php echo $cart["ID"]; ?>">
                <button type="submit" class="btn btn-success">Checkout</button>
            </form>
        </div>
    <?php } ?>
    <a href="cartHistory.php" class="btn btn-success">View Order History</a>
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