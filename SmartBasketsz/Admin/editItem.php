<?php
include_once "../config.php";
session_start();

if (!isset($_SESSION["email"])) {
    header("Location: login.php");
    exit();
}

// Check if an item ID is provided
if (!isset($_GET['ID']) || !is_numeric($_GET['ID'])) {
    header("Location: items.php");
    exit();
}

$itemId = (int)$_GET['ID'];

// Fetch item info
$sql = "SELECT * FROM Item WHERE ID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $itemId);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows <= 0) {
    echo "Item not found!";
    exit();
}
$item = $result->fetch_assoc();
$stmt->close();

// Process the form submission for updating an item
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Retrieve and (ideally) validate form data
    $name        = $_POST['name'];
    $description = $_POST['description'];
    $category    = $_POST['category'];
    $basePrice   = $_POST['basePrice'];
    $salePrice   = $_POST['salePrice'];

    // Image processing: update the image if a new one is uploaded.
    if (isset($_FILES['image']) && !empty($_FILES['image']['name'])) {
        $fileName      = $_FILES['image']['name'];
        $fileTmpName   = $_FILES['image']['tmp_name'];
        $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);

        // Generate a unique file name (here using the item ID and the current timestamp)
        $newFileName   = $itemId . "_item_" . time() . "." . $fileExtension;
        $destination   = "../ItemImages/" . $newFileName;

        if (move_uploaded_file($fileTmpName, $destination)) {
            // Delete the old image if it exists (optional)
            if (!empty($item['image']) && file_exists("../ItemImages/" . $item['image'])) {
                unlink("../ItemImages/" . $item['image']);
            }
            $image = $newFileName;
        } else {
            echo "<script>alert('Error uploading image file.');</script>";
            exit();
        }
    } else {
        // No new image uploaded, use the current one
        $image = $item['image'];
    }

    // Update the item record in the database using a prepared statement
    $sqlUpdate = "UPDATE Item SET name = ?, description = ?, category = ?, basePrice = ?, salePrice = ?, image = ? WHERE ID = ?";
    $stmtUpdate = $conn->prepare($sqlUpdate);
    if ($stmtUpdate === false) {
        error_log("Prepare failed: " . $conn->error);
        exit("Database error.");
    }

    // Bind parameters.
    // Assuming basePrice and salePrice are decimals and quantity is integer.
    $stmtUpdate->bind_param("sssddsi", $name, $description, $category, $basePrice, $salePrice, $image, $itemId);

    if ($stmtUpdate->execute()) {
        echo "<script>alert('Item updated successfully!'); window.location.href='items.php';</script>";
        exit();
    } else {
        error_log("Execute failed: " . $stmtUpdate->error);
        echo "<script>alert('Error updating item.');</script>";
    }
    $stmtUpdate->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Item</title>
    <link rel="icon" type="image/x-icon" href="../SmartBasketszLogo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
          crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
            crossorigin="anonymous"></script>
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
    <a class="flex-sm-fill text-sm-center nav-link" href="dashboard.php">Dashboard</a>
    <a class="flex-sm-fill text-sm-center nav-link" href="profile.php">Profile</a>
    <a class="flex-sm-fill text-sm-center nav-link active" href="items.php">Items</a>
    <a class="flex-sm-fill text-sm-center nav-link" href="carts.php">Carts</a>
    <a class="flex-sm-fill text-sm-center nav-link" href="messageViewer.php">Message Viewer</a>
    <a class="flex-sm-fill text-sm-center nav-link" href="users.php">Users</a>
    <a class="flex-sm-fill text-sm-center nav-link" href="logout.php">Logout</a>
</nav>
<div class="container bg-light mt-4">
    <h2>Edit Item</h2>
    <form action="editItem.php?ID=<?php echo $itemId; ?>" method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="name" class="form-label">Name</label>
            <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($item['name']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea class="form-control" id="description" name="description" required><?php echo htmlspecialchars($item['description']); ?></textarea>
        </div>
        <div class="mb-3">
            <label for="category" class="form-label">Category</label>
            <input type="text" class="form-control" id="category" name="category" value="<?php echo htmlspecialchars($item['category']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="basePrice" class="form-label">Base Price (RM)</label>
            <input type="number" step="0.01" class="form-control" id="basePrice" name="basePrice" value="<?php echo htmlspecialchars($item['basePrice']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="salePrice" class="form-label">Sale Price (RM)</label>
            <input type="number" step="0.01" class="form-control" id="salePrice" name="salePrice" value="<?php echo htmlspecialchars($item['salePrice']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="currentImage" class="form-label">Current Image</label><br>
            <?php if (!empty($item['image']) && file_exists("../ItemImages/" . $item['image'])): ?>
                <img src="../ItemImages/<?php echo $item['image']; ?>" alt="Item Image" style="max-width:150px;">
            <?php else: ?>
                No image available.
            <?php endif; ?>
        </div>
        <div class="mb-3">
            <label for="image" class="form-label">Change Image</label>
            <input type="file" class="form-control" id="image" name="image">
        </div>
        <a href="items.php" class="btn btn-success">Back</a>
        <button type="submit" class="btn btn-success">Update Item</button>
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