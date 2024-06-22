<?php
session_start();

include 'db.php';
include 'auth_functions.php';
// checkRole('stock_manager');

if (isset($_SESSION['user_role'])) {
    echo "Current user role: " . $_SESSION['user_role'] . "<br>";
} else {
    echo "No user role found in session.<br>";
}

// checkRole('stock_manager');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $serial_number = $_POST['serial_number'];
    $quantity_requested = $_POST['quantity_requested'];
    $taken_by = $_POST['taken_by'];
    $is_paid = $_POST['is_paid'];
    $payment_method = $_POST['payment_method'];
    $reason = $_POST['reason'];

    $query = "SELECT quantity FROM items WHERE serial_number = ?";
    $stmt = $conn->prepare($query); 
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param('s', $serial_number);
    $stmt->execute();
    $result = $stmt->get_result();
    $item = $result->fetch_assoc();

    if ($item && $item['quantity'] >= $quantity_requested) {
        $insert_query = "INSERT INTO transactions (item_id, taken_by, is_paid, payment_method, reason) VALUES ((SELECT id FROM items WHERE serial_number = ?), ?, ?, ?, ?)";
        $insert_stmt = $conn->prepare($insert_query);
        if (!$insert_stmt) {
            die("Prepare failed: " . $conn->error);
        }
        $insert_stmt->bind_param('sssss', $serial_number, $taken_by, $is_paid, $payment_method, $reason);
        $insert_stmt->execute();

        $update_query = "UPDATE items SET quantity = quantity - ? WHERE serial_number = ?";
        $update_stmt = $conn->prepare($update_query);
        if (!$update_stmt) {
            die("Prepare failed: " . $conn->error);
        }
        $update_stmt->bind_param('is', $quantity_requested, $serial_number);
        $update_stmt->execute();

        echo "Item checked out successfully!";
    } else {
        echo "Insufficient stock for the requested quantity!";
    }
}
?>

<form method="POST">
    Serial Number: <input type="text" name="serial_number" required><br>
    Quantity Requested: <input type="number" name="quantity_requested" required><br>
    Taken By: <input type="text" name="taken_by" required><br>
    Is Paid: 
    <select name="is_paid">
        <option value="yes">Yes</option>
        <option value="no">No</option>
    </select><br>
    Payment Method:
    <select name="payment_method">
        <option value="cash">Cash</option>
        <option value="cheque">Cheque</option>
        <option value="momo">Momo</option>
        <option value="n/a">N/A</option>
    </select><br>
    Reason: <input type="text" name="reason"><br>
    <button type="submit">Check Out Item</button>
</form>
