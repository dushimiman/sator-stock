<?php
session_start();
include(__DIR__ . '/../includes/nav_bar.php');
include(__DIR__ . '/../includes/db.php'); 
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    header("Location: ../login.php"); 
    exit();
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $request_id = $_POST['request_id'];
    $item_name = $_POST['item_name'];

    if ($item_name === 'GPS TRACKERS') {
        $imei = $_POST['imei'];
        
        $check_sql = "SELECT * FROM stock WHERE item_name = ? AND serial_number = ?";
        $check_stmt = $conn->prepare($check_sql);
        
        if ($check_stmt === false) {
            die("Error preparing the query: " . $conn->error);
        }
        
        $check_stmt->bind_param("ss", $item_name, $imei);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        
        if ($check_result->num_rows == 0) {
            die("IMEI $imei is not available in stock for GPS TRACKERS.");
        }
        
        // Insert into out_in_stock table
        $insert_sql = "INSERT INTO out_in_stock (id, item_name, imei) VALUES (?, ?, ?)";
        $insert_stmt = $conn->prepare($insert_sql);
        
        if ($insert_stmt === false) {
            die("Error preparing the query: " . $conn->error);
        }
        
        $insert_stmt->bind_param("iss", $request_id, $item_name, $imei);
        $insert_stmt->execute();
        
        $insert_stmt->close();
        
        // Delete from stock table (assuming you want to remove this specific IMEI)
        $delete_sql = "DELETE FROM stock WHERE item_name = ? AND serial_number = ?";
        $delete_stmt = $conn->prepare($delete_sql);
        
        if ($delete_stmt === false) {
            die("Error preparing the query: " . $conn->error);
        }
        
        $delete_stmt->bind_param("ss", $item_name, $imei);
        $delete_stmt->execute();
        
        $delete_stmt->close();
        
    } elseif ($item_name === 'SPEED GOVERNORS') {
        $serial_number = $_POST['serial_number'];
        
        // Check if the serial number exists in stock for SPEED GOVERNORS
        $check_sql = "SELECT * FROM stock WHERE item_name = ? AND serial_number = ?";
        $check_stmt = $conn->prepare($check_sql);
        
        if ($check_stmt === false) {
            die("Error preparing the query: " . $conn->error);
        }
        
        $check_stmt->bind_param("ss", $item_name, $serial_number);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        
        if ($check_result->num_rows == 0) {
            die("Serial number $serial_number is not available in stock for SPEED GOVERNORS.");
        }
        
        // Insert into out_in_stock table
        $insert_sql = "INSERT INTO out_in_stock (id, item_name, serial_number) VALUES (?, ?, ?)";
        $insert_stmt = $conn->prepare($insert_sql);
        
        if ($insert_stmt === false) {
            die("Error preparing the query: " . $conn->error);
        }
        
        $insert_stmt->bind_param("iss", $request_id, $item_name, $serial_number);
        $insert_stmt->execute();
        
        $insert_stmt->close();
        
        // Delete from stock table (assuming you want to remove this specific serial number)
        $delete_sql = "DELETE FROM stock WHERE item_name = ? AND serial_number = ?";
        $delete_stmt = $conn->prepare($delete_sql);
        
        if ($delete_stmt === false) {
            die("Error preparing the query: " . $conn->error);
        }
        
        $delete_stmt->bind_param("ss", $item_name, $serial_number);
        $delete_stmt->execute();
        
        $delete_stmt->close();
        
    } else {
        // Handle other items (Quantity input)
        $quantity = $_POST['quantity'];
        
        // Check if requested quantity is available in stock
        $check_sql = "SELECT SUM(quantity) AS total_quantity FROM stock WHERE item_name = ?";
        $check_stmt = $conn->prepare($check_sql);
        
        if ($check_stmt === false) {
            die("Error preparing the query: " . $conn->error);
        }
        
        $check_stmt->bind_param("s", $item_name);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        $row = $check_result->fetch_assoc();
        $total_quantity = $row['total_quantity'];
        
        if ($total_quantity < $quantity) {
            die("Requested quantity ($quantity) exceeds available stock ($total_quantity) for $item_name.");
        }
        
        // Insert into out_in_stock table
        $insert_sql = "INSERT INTO out_in_stock (id, item_name, quantity) VALUES (?, ?, ?)";
        $insert_stmt = $conn->prepare($insert_sql);
        
        if ($insert_stmt === false) {
            die("Error preparing the query: " . $conn->error);
        }
        
        $insert_stmt->bind_param("isi", $request_id, $item_name, $quantity);
        $insert_stmt->execute();
        
        $insert_stmt->close();
        
        // Update stock quantity (subtract requested quantity)
        $update_sql = "UPDATE stock SET quantity = quantity - ? WHERE item_name = ? LIMIT ?";
        $update_stmt = $conn->prepare($update_sql);
        
        if ($update_stmt === false) {
            die("Error preparing the query: " . $conn->error);
        }
        
        $update_stmt->bind_param("isi", $quantity, $item_name, $quantity);
        $update_stmt->execute();
        
        $update_stmt->close();
    }
    
    // Redirect to view_out_in_stock.php after successful processing
    header("Location: view_out_in_stock.php");
    exit();
    
} else {
    die("Invalid request method.");
}

$conn->close();
?>
