<?php
include_once "../config.php";
session_start();

if (!isset($_SESSION["email"])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['ID']) || !is_numeric($_GET['ID'])) {
    header("Location: items.php");
    exit();
}

$itemId = (int)$_GET['ID'];

// Retrieve the item to delete (and its image information)
$sql = "SELECT * FROM Item WHERE ID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $itemId);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows <= 0) {
    echo "<script>alert('Item not found.'); window.location.href='items.php';</script>";
    exit();
}
$item = $result->fetch_assoc();
$stmt->close();

// Delete the item from the database
$sqlDelete = "DELETE FROM Item WHERE ID = ?";
$stmtDelete = $conn->prepare($sqlDelete);
if ($stmtDelete === false) {
    error_log("Prepare failed: " . $conn->error);
    exit("Database error.");
}
$stmtDelete->bind_param("i", $itemId);
if ($stmtDelete->execute()) {
    // Optionally, delete the image file if it exists
    if (!empty($item['image']) && file_exists("../ItemImages/" . $item['image'])) {
        unlink("../ItemImages/" . $item['image']);
    }
    echo "<script>alert('Item deleted successfully!'); window.location.href='items.php';</script>";
    exit();
} else {
    error_log("Execute failed: " . $stmtDelete->error);
    echo "<script>alert('Error deleting item'); window.location.href='items.php';</script>";
}
$stmtDelete->close();
?>