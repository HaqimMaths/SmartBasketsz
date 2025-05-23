<?php
include_once "../config.php";
session_start();

if (!isset($_SESSION["email"])) {
    header("Location: login.php");
    exit();
}

// Process the form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Retrieve and (ideally) validate form data
    $name        = $_POST['name'];
    $description = $_POST['description'];
    $category    = $_POST['category'];
    $basePrice   = $_POST['basePrice'];
    $salePrice   = $_POST['salePrice'];

    // Check if an image file was uploaded
    $fileUploaded = (isset($_FILES['image']) && !empty($_FILES['image']['name']));

    // Insert the new item into the database first with an empty image field
    // If there's no image, we insert an empty string.
    $initialImage = "";
    $sqlInsert = "INSERT INTO Item (name, description, category, basePrice, salePrice, image) VALUES (?, ?, ?, ?, ?, ?)";
    $stmtInsert = $conn->prepare($sqlInsert);
    if ($stmtInsert === false) {
        error_log("Prepare failed: " . $conn->error);
        exit("Database error.");
    }

    $stmtInsert->bind_param("sssdds", $name, $description, $category, $basePrice, $salePrice, $initialImage);

    if ($stmtInsert->execute()) {
        // Get the newly inserted item ID
        $newItemId = $conn->insert_id;

        // If an image was uploaded, process the file upload now.
        if ($fileUploaded) {
            $fileName      = $_FILES['image']['name'];
            $fileTmpName   = $_FILES['image']['tmp_name'];
            $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);

            // Generate a unique file name using the new item ID at the front.
            $newFileName   = $newItemId . "_item_" . time() . "." . $fileExtension;
            $destination   = "../ItemImages/" . $newFileName;

            if (move_uploaded_file($fileTmpName, $destination)) {
                // Update the inserted record with the correct image filename.
                $sqlUpdate = "UPDATE Item SET image = ? WHERE ID = ?";
                $stmtUpdate = $conn->prepare($sqlUpdate);
                if ($stmtUpdate !== false) {
                    $stmtUpdate->bind_param("si", $newFileName, $newItemId);
                    $stmtUpdate->execute();
                    $stmtUpdate->close();
                } else {
                    error_log("Update Prepare failed: " . $conn->error);
                }
            } else {
                echo "<script>alert('Error uploading image file.');</script>";
                exit();
            }
        }

        echo "<script>alert('Item added successfully!'); window.location.href='items.php';</script>";
        exit();
    } else {
        error_log("Execute failed: " . $stmtInsert->error);
        echo "<script>alert('Error adding item.');</script>";
    }
    $stmtInsert->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add New Item</title>
    <link rel="icon" type="image/x-icon" href="../SmartBasketszLogo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC"
          crossorigin="anonymous">
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
<nav class="nav nav-pills flex-column flex-sm-row theNavBar">
    <a class="flex-sm-fill text-sm-center nav-link" href="profile.php">Profile</a>
    <a class="flex-sm-fill text-sm-center nav-link" href="dashboard.php">Dashboard</a>
    <a class="flex-sm-fill text-sm-center nav-link active" href="items.php">Items</a>
    <a class="flex-sm-fill text-sm-center nav-link" href="carts.php">Carts</a>
    <a class="flex-sm-fill text-sm-center nav-link" href="messageViewer.php">Message Viewer</a>
    <a class="flex-sm-fill text-sm-center nav-link" href="users.php">Users</a>
    <a class="flex-sm-fill text-sm-center nav-link" href="logout.php">Logout</a>
</nav>
<div class="container bg-light mt-4">
    <h2>Add New Item</h2>
    <form action="addItem.php" method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="name" class="form-label">Name</label>
            <input type="text" class="form-control" id="name" name="name" placeholder="Enter item name" required>
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea class="form-control" id="description" name="description" placeholder="Enter item description" required></textarea>
        </div>
        <div class="mb-3">
            <label for="category" class="form-label">Category</label>
            <input type="text" class="form-control" id="category" name="category" placeholder="Enter item category" required>
        </div>
        <div class="mb-3">
            <label for="basePrice" class="form-label">Base Price (RM)</label>
            <input type="number" step="0.01" class="form-control" id="basePrice" name="basePrice" placeholder="E.g., 100.00" required>
        </div>
        <div class="mb-3">
            <label for="salePrice" class="form-label">Sale Price (RM)</label>
            <input type="number" step="0.01" class="form-control" id="salePrice" name="salePrice" placeholder="E.g., 80.00" required>
        </div>
        <div class="mb-3">
            <label for="image" class="form-label">Image</label>
            <input type="file" class="form-control" id="image" name="image">
        </div>
        <a href="items.php" class="btn btn-success">Back</a>
        <button type="submit" class="btn btn-success">Add Item</button>
    </form>
</div>
<div class="container-fluid d-flex justify-content-center align-items-center footer-bar">
    <footer>
        <p>&copy; 2025 - SmartBasketsz</p>
        <address><i>Management and Science University, University Drive, Off Persiaran Sukan, Section 13, 40100 Shah Alam Selangor Malaysia</i></address>
    </footer>
</div>
</body>
</html>