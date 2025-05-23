<?php
include_once "../config.php";
session_start();

if (!isset($_SESSION["email"])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['ID']) || !is_numeric($_GET['ID'])) {
    header("Location: messageViewer.php");
    exit();
}

$contactFormId = (int)$_GET['ID'];

// Retrieve the item to delete (and its image information)
$sql = "SELECT * FROM ContactForm WHERE ID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $contactFormId);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows <= 0) {
    echo "<script>alert('Contact form not found.'); window.location.href='messageViewer.php';</script>";
    exit();
}
$contactForm = $result->fetch_assoc();
$stmt->close();

// Delete the item from the database
$sqlDelete = "DELETE FROM ContactForm WHERE ID = ?";
$stmtDelete = $conn->prepare($sqlDelete);
if ($stmtDelete === false) {
    error_log("Prepare failed: " . $conn->error);
    exit("Database error.");
}
$stmtDelete->bind_param("i", $contactFormId);
if ($stmtDelete->execute()) {
    // Optionally, delete the image file if it exists
    if (!empty($contactForm['image']) && file_exists("../ContactFormImages/" . $contactForm['image'])) {
        unlink("../ContactFormImages/" . $contactForm['image']);
    }
    echo "<script>alert('Contact form deleted successfully!'); window.location.href='messageViewer.php';</script>";
    exit();
} else {
    error_log("Execute failed: " . $stmtDelete->error);
    echo "<script>alert('Error deleting contact form'); window.location.href='messageViewer.php';</script>";
}
$stmtDelete->close();
?>