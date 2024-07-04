<?php
include('includes/db.php');


$requisition_date = $_POST['requisition_date'];
$requested_by = $_POST['requested_by'];
$item_name = $_POST['item_name'];
$quantity = $_POST['quantity'];
$location = $_POST['location'];
$reasons = $_POST['reasons'];
$status = 'pending'; 

$sql = "INSERT INTO requests (requisition_date, requested_by, item_name, quantity, location, reasons, status)
        VALUES ('$requisition_date', '$requested_by', '$item_name', '$quantity', '$location', '$reasons', '$status')";

if ($conn->query($sql) === TRUE) {
    echo "Request submitted successfully.";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>
