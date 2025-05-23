<?php
include_once "../config.php";
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Retrieve and trim form data.
    $firstName       = trim(isset($_POST['firstName']) ? $_POST['firstName'] : '');
    $lastName        = trim(isset($_POST['lastName']) ? $_POST['lastName'] : '');
    $address         = trim(isset($_POST['address']) ? $_POST['address'] : '');
    $country         = trim(isset($_POST['country']) ? $_POST['country'] : '');
    $email           = trim(isset($_POST['email']) ? $_POST['email'] : '');
    $phoneNumber     = trim(isset($_POST['phoneNumber']) ? $_POST['phoneNumber'] : '');
    $password        = isset($_POST['password']) ? $_POST['password'] : '';
    $confirmPassword = isset($_POST['confirmPassword']) ? $_POST['confirmPassword'] : '';

    // Validate required fields.
    if (empty($firstName) || empty($lastName) || empty($address) || empty($country) ||
        empty($email) || empty($phoneNumber) || empty($password) || empty($confirmPassword)) {
        $_SESSION["error"] = "Please fill in all required fields.";
        header("Location: register.php");
        exit();
    }

    // Validate email format.
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION["error"] = "Invalid email address.";
        header("Location: register.php");
        exit();
    }

    // Ensure passwords match.
    if ($password !== $confirmPassword) {
        $_SESSION["error"] = "Passwords do not match.";
        header("Location: register.php");
        exit();
    }

    // Check if email already exists.
    $sqlCheck = "SELECT ID FROM Customer WHERE email = ?";
    $stmtCheck = $conn->prepare($sqlCheck);
    if ($stmtCheck) {
        $stmtCheck->bind_param("s", $email);
        $stmtCheck->execute();
        $resultCheck = $stmtCheck->get_result();
        if ($resultCheck->num_rows > 0) {
            $_SESSION["error"] = "Email address already registered.";
            header("Location: register.php");
            exit();
        }
        $stmtCheck->close();
    } else {
        $_SESSION["error"] = "Database error: " . $conn->error;
        header("Location: register.php");
        exit();
    }

    // Prepare file upload variables without moving file yet.
    $fileUpload = false;
    if (isset($_FILES['profilePicture']) && !empty($_FILES['profilePicture']['name'])) {
        $fileName       = $_FILES['profilePicture']['name'];
        $fileTmpName    = $_FILES['profilePicture']['tmp_name'];
        $fileError      = $_FILES['profilePicture']['error'];
        if ($fileError !== UPLOAD_ERR_OK) {
            $_SESSION["error"] = "Error uploading profile picture. Error code: " . $fileError;
            header("Location: register.php");
            exit();
        }
        $fileExtension  = pathinfo($fileName, PATHINFO_EXTENSION);
        $fileUpload     = true;
    }

    // Hash password.
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    // Insert a new customer record with an empty profilePicture.
    $emptyPicture = "";
    $sqlInsert = "INSERT INTO Customer (firstName, lastName, address, country, email, phoneNumber, passwordHash, profilePicture) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmtInsert = $conn->prepare($sqlInsert);
    if ($stmtInsert === false) {
        $_SESSION["error"] = "Database error: " . $conn->error;
        header("Location: register.php");
        exit();
    }
    $stmtInsert->bind_param("ssssssss", $firstName, $lastName, $address, $country, $email, $phoneNumber, $passwordHash, $emptyPicture);
    if ($stmtInsert->execute()) {
        // Retrieve the new customer ID.
        $customerID = $conn->insert_id;
    } else {
        $_SESSION["error"] = "Registration failed: " . $stmtInsert->error;
        header("Location: register.php");
        exit();
    }
    $stmtInsert->close();

    // Process and move profile picture upload if provided.
    if ($fileUpload) {
        // Filename now incorporates the customerID.
        $newFileName = "profile_" . $customerID . "." . $fileExtension;
        $destination = "ProfilePictures/" . $newFileName;
        if (!move_uploaded_file($fileTmpName, $destination)) {
            $_SESSION["error"] = "Error uploading profile picture. Verify the destination folder exists and has write permissions.";
            header("Location: register.php");
            exit();
        }
        // Update the customer's record with the new profile picture name.
        $sqlUpdate = "UPDATE Customer SET profilePicture = ? WHERE ID = ?";
        $stmtUpdate = $conn->prepare($sqlUpdate);
        if ($stmtUpdate === false) {
            $_SESSION["error"] = "Database error: " . $conn->error;
            header("Location: register.php");
            exit();
        }
        $stmtUpdate->bind_param("si", $newFileName, $customerID);
        if (!$stmtUpdate->execute()) {
            $_SESSION["error"] = "Failed to update profile picture: " . $stmtUpdate->error;
            header("Location: register.php");
            exit();
        }
        $stmtUpdate->close();
    }

    $_SESSION["message"] = "Registration successful! Please login.";
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Customer Registration</title>
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
<div class="container">
    <div class="row justify-content-center mt-5">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0">Customer Registration</h4>
                </div>
                <div class="card-body">
                    <?php
                    if(isset($_SESSION["error"])){
                        echo '<div class="alert alert-danger">' . $_SESSION["error"] . '</div>';
                        unset($_SESSION["error"]);
                    }
                    ?>
                    <form action="register.php" method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="firstName" class="form-label">First Name</label>
                            <input type="text" class="form-control" id="firstName" name="firstName" required>
                        </div>
                        <div class="mb-3">
                            <label for="lastName" class="form-label">Last Name</label>
                            <input type="text" class="form-control" id="lastName" name="lastName" required>
                        </div>
                        <div class="mb-3">
                            <label for="address" class="form-label">Address</label>
                            <textarea class="form-control" id="address" name="address" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="country" class="form-label">Country</label>
                            <input type="text" class="form-control" id="country" name="country" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="phoneNumber" class="form-label">Phone Number</label>
                            <input type="text" class="form-control" id="phoneNumber" name="phoneNumber" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <div class="mb-3">
                            <label for="confirmPassword" class="form-label">Confirm Password</label>
                            <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" required>
                        </div>
                        <div class="mb-3">
                            <label for="profilePicture" class="form-label">Profile Picture (optional)</label>
                            <input type="file" class="form-control" id="profilePicture" name="profilePicture">
                        </div>
                        <button type="submit" class="btn btn-success">Register</button>
                    </form>
                    <p class="mt-2">Already have an account? <a href="login.php">Login here</a>.</p>
                </div>
            </div>
        </div>
    </div>
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