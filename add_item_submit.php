<?php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "stock_management_system";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


$item_name = $_POST['item_name'];
$item_type = $_POST['item_type'];
$serial_number = $_POST['serial_number'] ?? null;
$quantity = $_POST['quantity'] ?? null;

// Validation
if ($item_name === "GPS TRACKERS") {
    if (!preg_match("/^[A-Z]{2}[0-9]{15}[A-Z]{2}$/", $serial_number)) {
        die("GPS TRACKERS serial number must be 2 characters, 15 numbers, 2 characters.");
    }
} elseif ($item_name === "SPEED GOVERNORS") {
    if (!preg_match("/^[A-Z]{2}[0-9]{11}$/", $serial_number)) {
        die("SPEED GOVERNORS serial number must be 2 characters, 11 numbers.");
    }
} elseif (is_null($quantity)) {
    die("Quantity is required for non-serial items.");
}

// Insert data into stock table
$sql = "INSERT INTO stock (item_name, item_type, serial_number, quantity, branch) VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    die("Error preparing the query: " . $conn->error);
}

$branch = 'default_branch'; // Adjust this as needed
$stmt->bind_param("sssis", $item_name, $item_type, $serial_number, $quantity, $branch);
$stmt->execute();
$stmt->close();

echo "Item successfully added to stock.";

$conn->close();
?>
