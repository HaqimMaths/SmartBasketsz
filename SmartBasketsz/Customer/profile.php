<?php
include_once "../config.php";
session_start();

if (!isset($_SESSION["email"])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Use the session id, assuming it is stored as an integer.
    $customerId = (int)$_SESSION['ID'];
    $firstName   = $_POST['firstName'];
    $lastName    = $_POST['lastName'];
    $address     = $_POST['address'];
    $country     = $_POST['country'];
    $email       = $_POST['email'];
    $phoneNumber = $_POST['phoneNumber'];
    $password    = $_POST['password'];

    // Process file upload if provided:
    if (isset($_FILES['profilePicture']) && !empty($_FILES['profilePicture']['name'])) {
        // Save the old profile picture file name before uploading a new one.
        $oldProfilePicture = isset($_SESSION['profilePicture']) ? $_SESSION['profilePicture'] : "";

        $fileName      = $_FILES['profilePicture']['name'];
        $fileTmpName   = $_FILES['profilePicture']['tmp_name'];
        $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);

        // Use the user's ID for naming; time() ensures uniqueness.
        $newFileName   = $customerId . "_ProfilePicture_" . time() . "." . $fileExtension;
        $destination   = "./ProfilePictures/" . $newFileName;

        if (move_uploaded_file($fileTmpName, $destination)) {
            $profilePicture = $newFileName;
            $_SESSION['profilePicture'] = $profilePicture;

            // Delete the old profile picture if it exists and is different.
            if (!empty($oldProfilePicture) && $oldProfilePicture !== $newFileName) {
                $oldPicturePath = "./ProfilePictures/" . $oldProfilePicture;
                if (file_exists($oldPicturePath)) {
                    unlink($oldPicturePath);
                }
            }
        } else {
            echo "<script>alert('There was an error uploading your file.');</script>";
            exit();
        }
    } else {
        $profilePicture = $_SESSION['profilePicture'];
    }

    // Hash the new password (if you store the hash rather than plain text).
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Prepare the UPDATE statement using parameterized query.
    $sql = "UPDATE Customer 
            SET firstName = ?, 
                lastName = ?, 
                address = ?, 
                country = ?, 
                email = ?, 
                phoneNumber = ?, 
                passwordHash = ?, 
                profilePicture = ? 
            WHERE ID = ?";

    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        error_log("Prepare failed: " . $conn->error);
        exit("Database error. Please try again later.");
    }

    // There are nine placeholders. The types: 8 strings and 1 integer.
    $stmt->bind_param("ssssssssi",
        $firstName,
        $lastName,
        $address,
        $country,
        $email,
        $phoneNumber,
        $hashedPassword,
        $profilePicture,
        $customerId);

    if ($stmt->execute()) {
        // (Optional) Update session with the new details so that the profile page shows updated info.
        $_SESSION['firstName']   = $firstName;
        $_SESSION['lastName']    = $lastName;
        $_SESSION['address']     = $address;
        $_SESSION['country']     = $country;
        $_SESSION['email']       = $email;
        $_SESSION['phoneNumber'] = $phoneNumber;
        $_SESSION['password']    = $password; // if you must (though storing plain text in session is not ideal)
        echo "<script>
                window.location.href = 'profile.php';
                alert('Profile updated successfully!');
              </script>";
        exit();
    } else {
        error_log("Execute failed: " . $stmt->error);
        echo "<script>alert('Error updating profile: " . $stmt->error . "');</script>";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Profile</title>
    <link rel="icon" type="image/x-icon" href="../SmartBasketszLogo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" type="text/css" href="../template.css?v=202505191827">
    <script src="../profileSeePassword.js" type="text/javascript"></script>
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
    <a class="flex-sm-fill text-sm-center nav-link active" href="profile.php">Profile</a>
    <a class="flex-sm-fill text-sm-center nav-link" href="items.php">Items</a>
    <a class="flex-sm-fill text-sm-center nav-link" href="cart.php">Cart</a>
    <a class="flex-sm-fill text-sm-center nav-link" href="contactForm.php">Contact Form</a>
    <a class="flex-sm-fill text-sm-center nav-link" href="logout.php">Logout</a>
</nav>
<div class="profileBox container rounded bg-white mt-5 mx-auto">
    <form class="row g-3" action="profile.php" method="POST" enctype="multipart/form-data">
        <div class="col-12">
            <img src="ProfilePictures/<?php echo $_SESSION['profilePicture'] ?>" class="img-thumbnail" height="200" width="200" alt="profile picture">
            <div class="mb-3">
                <label for="formFile" class="form-label">Change profile picture</label>
                <input class="form-control" type="file" id="profilePicture" name="profilePicture">
            </div>
        </div>
        <div class="col-md-4">
            <label for="id" class="form-label">ID</label>
            <input type="text" class="form-control" id="ID" name="ID" value="<?php echo $_SESSION['ID']; ?>" disabled>
        </div>
        <div class="col-md-4">
            <label for="firstName" class="form-label">First Name</label>
            <input type="text" class="form-control" id="firstName" name="firstName" value="<?php echo $_SESSION['firstName']; ?>">
        </div>
        <div class="col-md-4">
            <label for="lastName" class="form-label">Last Name</label>
            <input type="text" class="form-control" id="lastName" name="lastName" value="<?php echo $_SESSION['lastName']; ?>">
        </div>
        <div class="col-md-6">
            <label for="address" class="form-label">Address</label>
            <input type="text" class="form-control" id="address" name="address" value="<?php echo $_SESSION['address']; ?>">
        </div>
        <div class="col-md-6">
            <label for="country" class="form-label">Country</label>
            <input type="text" class="form-control" id="country" name="country" value="<?php echo $_SESSION['country']; ?>">
        </div>
        <div class="col-md-6">
            <label for="email" class="form-label">Email</label>
            <input type="text" class="form-control" id="email" name="email" value="<?php echo $_SESSION['email']; ?>">
        </div>
        <div class="col-md-6">
            <label for="phoneNumber" class="form-label">Phone Number</label>
            <input type="text" class="form-control" id="phoneNumber" name="phoneNumber" value="<?php echo $_SESSION['phoneNumber']; ?>">
        </div>
        <div class="col-12">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control" id="password" name="password" value="<?php echo $_SESSION['password']; ?>">
            <div class="form-check mt-2">
                <input type="checkbox" class="form-check-input" id="showPasswordCheckbox" onclick="togglePassword()">
                <label class="form-check-label" for="showPasswordCheckbox">Show Password</label>
            </div>
        </div>
        <div class="col-12">
            <button type="submit" class="btn btn-success">Save</button>
        </div>
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