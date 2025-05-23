<?php
include_once "../config.php";
session_start();

// Ensure the user is logged in.
if (!isset($_SESSION["email"])) {
    header("Location: login.php");
    exit();
}

// Process only POST requests.
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: items.php");
    exit();
}

// Retrieve and validate the posted item ID and requested quantity.
$itemId = intval($_POST["ID"]);
$orderQuantity = intval($_POST["quantity"]);

if ($itemId <= 0 || $orderQuantity <= 0) {
    $_SESSION["error"] = "Invalid item selection or quantity.";
    header("Location: items.php");
    exit();
}

// Retrieve the selected item's details (name, salePrice and stock quantity).
$sql = "SELECT name, salePrice FROM item WHERE ID = $itemId";
$result = $conn->query($sql);
if (!$result || $result->num_rows === 0) {
    $_SESSION["error"] = "Item not found.";
    header("Location: items.php");
    exit();
}
$item = $result->fetch_assoc();

// Check if the customer already has an active cart.
// (For this simple example, we store and reuse a cart by saving its ID in the session.)
if (isset($_SESSION["cart_id"])) {
    $cartId = $_SESSION["cart_id"];
} else {
    // Create a new cart.
    $customerId = $_SESSION['ID']; // Ideally use numeric customerID.
    $orderDate  = date("Y-m-d H:i:s");
    $paymentMethod = "Pending"; // Set default payment status.

    // Initially set totalPrice = 0; we will update it after adding the order item.
    $sqlCart = "INSERT INTO cart (customerID, totalPrice, orderDate, paymentMethod)
                VALUES ('$customerId', 0, '$orderDate', '$paymentMethod')";

    if ($conn->query($sqlCart) === TRUE) {
        $cartId = $conn->insert_id;
        $_SESSION["cart_id"] = $cartId;
    } else {
        $_SESSION["error"] = "Failed to create cart: " . $conn->error;
        header("Location: items.php");
        exit();
    }
}

// Insert a new record into the orderItem table.
// We include the cartId to easily associate order items with a cart.
$customerId = $_SESSION['ID']; // Or use numeric customer id.
$sqlInsert = "INSERT INTO orderItem (customerID, itemId, quantity, cartId)
              VALUES ($customerId, $itemId, $orderQuantity, $cartId)";

if ($conn->query($sqlInsert) === TRUE) {
    // Recalculate totalPrice for the cart using the sum of (salePrice * quantity) for all orderItems in this cart.
    $sqlTotal = "SELECT SUM(i.salePrice * oi.quantity) AS total
                 FROM orderItem oi
                 JOIN item i ON oi.itemId = i.ID
                 WHERE oi.cartId = $cartId";
    $resultTotal = $conn->query($sqlTotal);
    $totalPrice = 0;
    if ($resultTotal && $rowTotal = $resultTotal->fetch_assoc()) {
        $totalPrice = $rowTotal["total"];
    }

    // Update the cart with the new total price.
    $sqlUpdateCart = "UPDATE cart SET totalPrice = $totalPrice WHERE ID = $cartId";
    if (!$conn->query($sqlUpdateCart)) {
        $_SESSION["error"] = "Order added, but failed to update cart total: " . $conn->error;
        header("Location: items.php");
        exit();
    }

    $_SESSION["message"] = "Item added to cart successfully!";
    header("Location: items.php");
    exit();
} else {
    $_SESSION["error"] = "Failed to add item to cart: " . $conn->error;
    header("Location: items.php");
    exit();
}
?>