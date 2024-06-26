<?php
session_start();
include 'db.php'; 
include 'auth_functions.php'; 

if (isset($_SESSION['user_role'])) {
    echo "Current user role: " . $_SESSION['user_role'] . "<br>";
} else {
    echo "No user role found in session.<br>";
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $serial_number = $_POST['serial_number'];
    $returned_by = $_POST['returned_by'];
    $reason = $_POST['reason'];

    // Check if the item exists in items_sold
    $check_query = "SELECT id, name FROM items_sold WHERE serial_number = ?";
    $check_stmt = $conn->prepare($check_query);
    if (!$check_stmt) {
        die("Prepare failed: " . $conn->error);
    }
    $check_stmt->bind_param('s', $serial_number);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    $item = $check_result->fetch_assoc();

    if (!$item) {
        echo "The item with serial number '$serial_number' is not found in sold items!";
        exit(); 
    }

    // Add the item back to the items table
    $insert_query = "INSERT INTO items (name, serial_number) VALUES (?, ?)";
    $insert_stmt = $conn->prepare($insert_query);
    if (!$insert_stmt) {
        die("Prepare failed: " . $conn->error);
    }
    $insert_stmt->bind_param('ss', $item['name'], $serial_number);
    $insert_stmt->execute();

    // Delete the item from items_sold table
    $delete_query = "DELETE FROM items_sold WHERE id = ?";
    $delete_stmt = $conn->prepare($delete_query);
    if (!$delete_stmt) {
        die("Prepare failed: " . $conn->error);
    }
    $delete_stmt->bind_param('i', $item['id']);
    $delete_stmt->execute();

    // Record the return transaction
    $return_query = "INSERT INTO return_transactions (item_id, returned_by, reason) VALUES (?, ?, ?)";
    $return_stmt = $conn->prepare($return_query);
    if (!$return_stmt) {
        die("Prepare failed: " . $conn->error);
    }
    $return_stmt->bind_param('iss', $item['id'], $returned_by, $reason);
    $return_stmt->execute();

    echo "Item returned successfully!";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Return Item</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h2>Return Item</h2>
        <form method="POST">
            Serial Number: <input type="text" name="serial_number" required><br>
            Returned By: <input type="text" name="returned_by" required><br>
            Reason: <input type="text" name="reason"><br>
            <button type="submit" class="btn btn-primary">Return Item</button>
        </form>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
