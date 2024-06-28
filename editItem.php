<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "stock_management_system";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $item_id = $_POST['item_id'];
    $name = $_POST['edit_name'];
    $type = $_POST['edit_type'];
    $serial_number = $_POST['edit_serial_number'];
   
    $edited_by = $_SESSION['username']; 

   
    $stmt = $conn->prepare("UPDATE items SET name=?, type=?, serial_number=?, last_edited_by=? WHERE id=?");
    if ($stmt === false) {
        die("Error preparing statement: " . $conn->error);
    }
    $stmt->bind_param("ssssi", $name, $type, $serial_number, $edited_by, $item_id);

  
    if ($stmt->execute()) {
        echo "Item updated successfully.";
    } else {
        echo "Error updating item: " . $stmt->error;
    }

  
    $stmt->close();
}

$conn->close();
?>
