<?php
// Assuming you have established a session where the username is stored upon login
session_start();

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
$quantity = $_POST['quantity'] ?? 0; // Assuming quantity is a number
$location = $_POST['location'] ?? '';
$payment_description = $_POST['payment_description'] ?? '';
$reasons = $_POST['reasons'] ?? '';
$status = $_POST['status'] ?? ''; // Assuming 'status' is part of your form

// Retrieve username from session
if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];

    $sql = "INSERT INTO requests (requisition_date, requested_by, item_name, quantity, location, payment_description, reasons, status, username) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        die("Error preparing the query: " . $conn->error);
    }

    $stmt->bind_param("sssiissss", $requisition_date, $requested_by, $item_name, $quantity, $location, $payment_description, $reasons, $status, $username);
    $executeResult = $stmt->execute();

    if ($executeResult) {
        echo "Request submitted successfully.";
    } else {
        echo "Error executing the query: " . $stmt->error;
    }

    $stmt->close();
} else {
    echo "Session error: User not logged in.";
}

$conn->close();
?>
