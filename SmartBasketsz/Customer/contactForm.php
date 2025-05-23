<?php
include_once "../config.php";
session_start();
if (!isset($_SESSION["email"])) {
    header("Location: login.php");
    exit();
}

// Process the form submission for adding a new contact form
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $customerName = trim(isset($_POST['customerName']) ? $_POST['customerName'] : '');
    $subject = trim(isset($_POST['subject']) ? $_POST['subject'] : '');
    $message_text = trim(isset($_POST['message']) ? $_POST['message'] : '');

    if (empty($customerName) || empty($subject) || empty($message_text)) {
        $_SESSION["error"] = "Please fill in all required fields.";
        header("Location: contactForm.php");
        exit();
    }

    $customerId = $_SESSION["ID"];
    $image = "";

    if (isset($_FILES['image']) && !empty($_FILES['image']['name'])) {
        $fileName = $_FILES['image']['name'];
        $fileTmpName = $_FILES['image']['tmp_name'];
        $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
        $newFileName = "contactForm_" . time() . "_" . uniqid() . "." . $fileExtension;
        $destination = "../ContactFormImages/" . $newFileName;

        if (move_uploaded_file($fileTmpName, $destination)) {
            $image = $newFileName;
        } else {
            echo "<script>alert('Error uploading image file.');</script>";
            exit();
        }
    }

    $sqlInsert = "INSERT INTO ContactForm (customerId, customerName, subject, message, image) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sqlInsert);
    if ($stmt === false) {
        error_log("Prepare failed: " . $conn->error);
        exit("Database error.");
    }
    $stmt->bind_param("sssss", $customerId, $customerName, $subject, $message_text, $image);

    if ($stmt->execute()) {
        echo "<script>alert('Contact form submitted successfully!'); window.location.href='contactForm.php';</script>";
        exit();
    } else {
        error_log("Execute failed: " . $stmt->error);
        echo "<script>alert('Error submitting contact form.');</script>";
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Contact Form</title>
    <link rel="icon" type="image/x-icon" href="../SmartBasketszLogo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
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
    <h2>Add New Contact Form</h2>
    <?php
    if (isset($_SESSION["error"])) {
        echo '<div class="alert alert-danger">' . $_SESSION["error"] . '</div>';
        unset($_SESSION["error"]);
    }
    ?>
    <form action="contactForm.php" method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="customerId" class="form-label">Customer ID</label>
            <input type="text" class="form-control" id="customerId" name="customerId"
                   value="<?php echo htmlspecialchars($_SESSION["ID"]); ?>" disabled>
        </div>
        <div class="mb-3">
            <label for="customerName" class="form-label">Customer Name</label>
            <input type="text" class="form-control" id="customerName" name="customerName"
                   value="<?php echo htmlspecialchars($_SESSION['firstName']. " ". $_SESSION['lastName'])?>" required>
        </div>
        <div class="mb-3">
            <label for="subject" class="form-label">Subject</label>
            <input type="text" class="form-control" id="subject" name="subject" required>
        </div>
        <div class="mb-3">
            <label for="message" class="form-label">Message</label>
            <textarea class="form-control" id="message" name="message" required></textarea>
        </div>
        <div class="mb-3">
            <label for="image" class="form-label">Upload Image (optional)</label>
            <input type="file" class="form-control" id="image" name="image">
        </div>
        <button type="submit" class="btn btn-success">Submit Contact Form</button>
        <a href="contactFormHistory.php" class="btn btn-success">View Past Contact Forms</a>
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