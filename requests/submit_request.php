<?php
session_start();
session_start();
include(__DIR__ . '/../includes/nav_bar.php');
include(__DIR__ . '/../includes/db.php'); 
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    header("Location: ../login.php"); 
    exit();
}


$requisition_date = !empty($_POST['requisition_date']) ? $_POST['requisition_date'] : null;
$requested_by = !empty($_POST['requested_by']) ? $_POST['requested_by'] : null;
$item_name = !empty($_POST['item_name']) ? $_POST['item_name'] : null;
$quantity = !empty($_POST['quantity']) ? (int)$_POST['quantity'] : 0;
$location = !empty($_POST['location']) ? $_POST['location'] : null;
$payment_description = !empty($_POST['payment_description']) ? $_POST['payment_description'] : null;
$reasons = !empty($_POST['reasons']) ? $_POST['reasons'] : null;
$status = !empty($_POST['status']) ? $_POST['status'] : null;

if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];

    $sql = "INSERT INTO requests (requisition_date, requested_by, item_name, quantity, location, payment_description, reasons, status, username) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $mysqli->prepare($sql);

    if ($stmt === false) {
        die("Error preparing the query: " . $mysqli->error);
    }

    $stmt->bind_param("sssiissss", $requisition_date, $requested_by, $item_name, $quantity, $location, $payment_description, $reasons, $status, $username);
    $executeResult = $stmt->execute();

    if ($executeResult) {
        header("Location: user_request.php");
        exit();
    } else {
        echo "Error executing the query: " . $stmt->error;
    }

    $stmt->close();
} else {
    echo "Session error: User not logged in.";
}

$mysqli->close();
?>
