<?php
$serverName = 'localhost:3307';
$userName = 'root';
$password = '';
$dbName = 'smartbasketsz';

// Create connection
$conn = mysqli_connect($serverName, $userName, $password, $dbName);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
//echo "Connected successfully";
?>