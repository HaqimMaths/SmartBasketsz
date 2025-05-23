<?php
include_once "../config.php";
session_start();
if (!isset($_SESSION["email"])) {
    header("Location: login.php");
    exit();
}
if (!isset($_GET['ID']) || !is_numeric($_GET['ID'])) {
    header("Location: contactFormHistory.php");
    exit();
}
$contactFormId = intval($_GET['ID']);
$customerID = $conn->real_escape_string($_SESSION["ID"]);
$sql = "SELECT * FROM ContactForm WHERE ID = ? AND customerId = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("is", $contactFormId, $customerID);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows <= 0) {
    echo "Contact form not found or not authorized!";
    exit();
}
$contactForm = $result->fetch_assoc();
$stmt->close();

// Process form submission for updating a contact form
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $customerName = trim(isset($_POST['customerName']) ? $_POST['customerName'] : '');
    $subject      = trim(isset($_POST['subject']) ? $_POST['subject'] : '');
    $message_text = trim(isset($_POST['message']) ? $_POST['message'] : '');
    if (empty($customerName) || empty($subject) || empty($message_text)) {
        $_SESSION["error"] = "All fields are required.";
        header("Location: editContactForm.php?ID=" . $contactFormId);
        exit();
    }
    if (isset($_FILES['image']) && !empty($_FILES['image']['name'])) {
        $fileName = $_FILES['image']['name'];
        $fileTmpName = $_FILES['image']['tmp_name'];
        $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
        $newFileName = $contactFormId . "_contactForm_" . time() . "." . $fileExtension;
        $destination = "../ContactFormImages/" . $newFileName;
        if (move_uploaded_file($fileTmpName, $destination)) {
            if (!empty($contactForm['image']) && file_exists("../ContactFormImages/" . $contactForm['image'])) {
                unlink("../ContactFormImages/" . $contactForm['image']);
            }
            $image = $newFileName;
        } else {
            echo "<script>alert('Error uploading image file.');</script>";
            exit();
        }
    } else {
        $image = $contactForm['image'];
    }
    $sqlUpdate = "UPDATE ContactForm SET customerName = ?, subject = ?, message = ?, image = ? WHERE ID = ? AND customerId = ?";
    $stmtUpdate = $conn->prepare($sqlUpdate);
    if ($stmtUpdate === false) {
        error_log("Prepare failed: " . $conn->error);
        exit("Database error.");
    }
    $stmtUpdate->bind_param("ssssis", $customerName, $subject, $message_text, $image, $contactFormId, $customerID);
    if ($stmtUpdate->execute()) {
        echo "<script>alert('Contact form updated successfully!'); window.location.href='contactFormHistory.php';</script>";
        exit();
    } else {
        error_log("Execute failed: " . $stmtUpdate->error);
        echo "<script>alert('Error updating contact form.');</script>";
    }
    $stmtUpdate->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Contact Form</title>
    <link rel="icon" type="image/x-icon" href="../SmartBasketszLogo.png">
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
    <a class="flex-sm-fill text-sm-center nav-link" href="cart.php">Cart</a>
    <a class="flex-sm-fill text-sm-center nav-link active" href="contactForm.php">Contact Form</a>
    <a class="flex-sm-fill text-sm-center nav-link" href="logout.php">Logout</a>
</nav>
<div class="container bg-light mt-4">
    <h2>Edit Contact Form</h2>
    <?php
    if(isset($_SESSION["error"])){
        echo '<div class="alert alert-danger">'.$_SESSION["error"].'</div>';
        unset($_SESSION["error"]);
    }
    ?>
    <form action="editContactForm.php?ID=<?php echo $contactFormId; ?>" method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="customerId" class="form-label">Customer ID</label>
            <input type="text" class="form-control" id="customerId" name="customerId" value="<?php echo htmlspecialchars($contactForm['customerId']); ?>" disabled>
        </div>
        <div class="mb-3">
            <label for="customerName" class="form-label">Customer Name</label>
            <input type="text" class="form-control" id="customerName" name="customerName" value="<?php echo htmlspecialchars($contactForm['customerName']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="subject" class="form-label">Subject</label>
            <input type="text" class="form-control" id="subject" name="subject" value="<?php echo htmlspecialchars($contactForm['subject']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="message" class="form-label">Message</label>
            <textarea class="form-control" id="message" name="message" required><?php echo htmlspecialchars($contactForm['message']); ?></textarea>
        </div>
        <div class="mb-3">
            <label for="currentImage" class="form-label">Current Image</label><br>
            <?php if (!empty($contactForm['image']) && file_exists("../ContactFormImages/" . $contactForm['image'])): ?>
                <img src="../ContactFormImages/<?php echo $contactForm['image']; ?>" alt="Contact Form Image" style="max-width:150px;">
            <?php else: ?>
                No image available.
            <?php endif; ?>
        </div>
        <div class="mb-3">
            <label for="image" class="form-label">Change Image (optional)</label>
            <input type="file" class="form-control" id="image" name="image">
        </div>
        <a href="contactForm.php" class="btn btn-success">Back</a>
        <button type="submit" class="btn btn-success">Update Contact Form</button>
    </form>
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