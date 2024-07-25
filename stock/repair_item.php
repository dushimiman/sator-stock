<?php
session_start();
include(__DIR__ . '/../includes/nav_bar.php');
include(__DIR__ . '/../includes/db.php');

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $sql = "UPDATE returned_items SET is_working = 1 WHERE id = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        header("Location: view_non_repair_item.php?message=Item+Repaired+Successfully");
    } else {
        echo "Error updating record: " . $mysqli->error;
    }

    $stmt->close();
}

$mysqli->close();
?>
