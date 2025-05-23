<?php
include_once "../config.php";
session_start();

if (!isset($_SESSION["email"])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['ID']) || !is_numeric($_GET['ID'])) {
    header("Location: carts.php");
    exit();
}

$cartId = (int)$_GET['ID'];

// Retrieve the item to delete (and its image information)
$sql = "SELECT * FROM Cart WHERE ID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $cartId);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows <= 0) {
    echo "<script>alert('Cart not found.'); window.location.href='carts.php';</script>";
    exit();
}
$contactForm = $result->fetch_assoc();
$stmt->close();

// Delete the item from the database
$sqlDelete = "DELETE FROM Cart WHERE ID = ?";
$stmtDelete = $conn->prepare($sqlDelete);
if ($stmtDelete === false) {
    error_log("Prepare failed: " . $conn->error);
    exit("Database error.");
}
$stmtDelete->bind_param("i", $cartId);
if ($stmtDelete->execute()) {
    echo "<script>alert('Cart deleted successfully!'); window.location.href='carts.php';</script>";
    exit();
} else {
    error_log("Execute failed: " . $stmtDelete->error);
    echo "<script>alert('Error deleting cart'); window.location.href='carts.php';</script>";
}
$stmtDelete->close();
?>