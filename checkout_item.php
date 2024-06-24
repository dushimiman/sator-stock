<?php
include('includes/nav_bar.php'); 
session_start();

include 'db.php'; 
include 'auth_functions.php'; 

if (isset($_SESSION['user_role'])) {
    echo "Current user role: " . $_SESSION['user_role'] . "<br>";
} else {
    echo "";
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $serial_number = $_POST['serial_number'];
    $taken_by = $_POST['taken_by'];
    $is_paid = $_POST['is_paid'];
    $payment_method = $_POST['payment_method'];
    $reason = $_POST['reason'];

    $conn->begin_transaction();

  
    $check_query = "SELECT id, name FROM items WHERE serial_number = ?";
    $check_stmt = $conn->prepare($check_query);
    if (!$check_stmt) {
        die("Prepare failed: " . $conn->error);
    }
    $check_stmt->bind_param('s', $serial_number);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    $item = $check_result->fetch_assoc();

    if (!$item) {
        echo "The item with serial number '$serial_number' is not found in stock!";
        $conn->rollback(); 
        exit(); 
    }



    
    $sold_query = "INSERT INTO items_sold (name, serial_number, taken_by, is_paid, payment_method, reason) VALUES (?, ?, ?, ?, ?, ?)";
    $sold_stmt = $conn->prepare($sold_query);
    if (!$sold_stmt) {
        die("Prepare failed: " . $conn->error);
    }
    $sold_stmt->bind_param('ssssss', $item['name'], $serial_number, $taken_by, $is_paid, $payment_method, $reason);
    if (!$sold_stmt->execute()) {
        echo "Error inserting into items_sold: " . $sold_stmt->error;
        $conn->rollback(); 
        exit();
    }


    $delete_query = "DELETE FROM items WHERE id = ?";
    $delete_stmt = $conn->prepare($delete_query);
    if (!$delete_stmt) {
        die("Prepare failed: " . $conn->error);
    }
    $delete_stmt->bind_param('i', $item['id']);
    if (!$delete_stmt->execute()) {
        echo "Error deleting from items: " . $delete_stmt->error;
        $conn->rollback(); 
        exit();
    }

   
    $conn->commit();

   
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Check Out Item</title>
   
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container ">
        <h2>Check Out Item</h2>
        <form method="POST">
            <div class="form-group">
                <label for="serial_number">Serial Number</label>
                <input type="text" class="form-control" id="serial_number" name="serial_number" required>
            </div>
            <div class="form-group">
                <label for="taken_by">Taken By</label>
                <input type="text" class="form-control" id="taken_by" name="taken_by" required>
            </div>
            <div class="form-group">
                <label for="is_paid">Is Paid</label>
                <select class="form-control" id="is_paid" name="is_paid">
                    <option value="yes">Yes</option>
                    <option value="no">No</option>
                </select>
            </div>
            <div class="form-group">
                <label for="payment_method">Payment Method</label>
                <select class="form-control" id="payment_method" name="payment_method">
                    <option value="cash">Cash</option>
                    <option value="cheque">Cheque</option>
                    <option value="momo">Momo</option>
                    <option value="n/a">N/A</option>
                </select>
            </div>
            <div class="form-group">
                <label for="reason">Reason</label>
                <input type="text" class="form-control" id="reason" name="reason">
            </div>
            <button type="submit" class="btn btn-primary">Check Out Item</button>
        </form>
    </div>

   
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
