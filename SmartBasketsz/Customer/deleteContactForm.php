<?php
include_once "../config.php";
session_start();
if (!isset($_SESSION["email"])) {
    header("Location: login.php");
    exit();
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['ID']) || !is_numeric($_POST['ID'])) {
    header("Location: contactFormHistory.php");
    exit();
}
$id = intval($_POST['ID']);
$customerID = $conn->real_escape_string($_SESSION["ID"]);

// Verify the contact form belongs to this user.
$sql = "SELECT * FROM ContactForm WHERE ID = ? AND customerId = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("is", $id, $customerID);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows <= 0) {
    $_SESSION["error"] = "No such contact form found or you are not authorized to delete it.";
    header("Location: contactFormHistory.php");
    exit();
}
$stmt->close();

// Delete record
$sqlDelete = "DELETE FROM ContactForm WHERE ID = ? AND customerId = ?";
$stmtDelete = $conn->prepare($sqlDelete);
$stmtDelete->bind_param("is", $id, $customerID);
if ($stmtDelete->execute()) {
    $_SESSION["message"] = "Contact form deleted successfully.";
} else {
    $_SESSION["error"] = "Error deleting contact form: " . $conn->error;
}
$stmtDelete->close();
header("Location: contactFormHistory.php");
exit();
?>