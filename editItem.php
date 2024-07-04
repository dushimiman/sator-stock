<?php
include('includes/db.php'); 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $item_id = $_POST['item_id'];
    $edit_name = $_POST['edit_name'];
    $edit_type = $_POST['edit_type'];
    $edit_serial_number = isset($_POST['edit_serial_number']) ? $_POST['edit_serial_number'] : null;
    $edit_quantity = isset($_POST['edit_quantity']) ? $_POST['edit_quantity'] : null;

   
    $stmt = $conn->prepare("UPDATE stock SET item_name = ?, item_type = ?, serial_number = ?, quantity = ? WHERE id = ?");
    if ($stmt === false) {
        die('Prepare failed: ' . htmlspecialchars($conn->error));
    }

    $bind = $stmt->bind_param("ssssi", $edit_name, $edit_type, $edit_serial_number, $edit_quantity, $item_id);
    if ($bind === false) {
        die('Bind param failed: ' . htmlspecialchars($stmt->error));
    }

    $execute = $stmt->execute();
    if ($execute === false) {
        die('Execute failed: ' . htmlspecialchars($stmt->error));
    }

    echo "Item updated successfully";

    $stmt->close();
    $conn->close();
    exit;
}
?>
