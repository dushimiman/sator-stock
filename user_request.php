<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "stock_management_system";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$requisition_date = $_POST['requisition_date'] ?? '';
$requested_by = $_POST['requested_by'] ?? '';
$item_name = $_POST['item_name'] ?? '';
$quantity = $_POST['quantity'] ?? 0;
$location = $_POST['location'] ?? '';
$payment_description = $_POST['payment_description'] ?? '';
$reasons = $_POST['reasons'] ?? '';

$status = isset($_POST['status']) ? $_POST['status'] : ''; 

$sql = "INSERT INTO requests (requisition_date, requested_by, item_name, quantity, location, payment_description, reasons, status) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    die("Error preparing the query: " . $conn->error);
}

$stmt->bind_param("sssiisss", $requisition_date, $requested_by, $item_name, $quantity, $location, $payment_description, $reasons, $status);
$executeResult = $stmt->execute();

if ($executeResult) {
    echo "Request submitted successfully.";
} else {
    echo "Error executing the query: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
