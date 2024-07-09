<?php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "stock_management_system";


$conn = new mysqli($servername, $username, $password, $dbname);


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


$requisition_date = $_POST['requisition_date'];
$requested_by = $_POST['requested_by'];
$item_name = $_POST['item_name'];
$quantity = $_POST['quantity'];
$location = $_POST['location'];
$payment_description = $_POST['payment_description'];
$reasons = $_POST['reasons'];

$sql = "INSERT INTO requests(requisition_date, requested_by, item_name, quantity, location, payment_description, reasons) 
        VALUES (?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    die("Error preparing the query: " . $conn->error);
}

$stmt->bind_param("sssiiss", $requisition_date, $requested_by, $item_name, $quantity, $location, $payment_description, $reasons);
$executeResult = $stmt->execute();
$stmt->close();
$conn->close();

if ($executeResult) {

    header("Location: user_request.php");
    exit();
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}
?>
