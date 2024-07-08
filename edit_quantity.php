<?php
include('includes/db.php');

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['request_id']) && isset($_POST['new_quantity'])) {
    $request_id = $_POST['request_id'];
    $new_quantity = $_POST['new_quantity'];
    $comment = $_POST['comment'];

  
    $sql_fetch_request = "SELECT * FROM requests WHERE id = $request_id";
    $result_fetch_request = $conn->query($sql_fetch_request);

    if ($result_fetch_request->num_rows == 1) {
        $request = $result_fetch_request->fetch_assoc();
        $current_quantity = $request['quantity'];

       
        if ($new_quantity != $current_quantity) {
          
            $sql_update_quantity = "UPDATE requests SET quantity = $new_quantity WHERE id = $request_id";
            if ($conn->query($sql_update_quantity) === TRUE) {
              
                $sql_log_change = "INSERT INTO quantity_change_log (request_id, old_quantity, new_quantity, comment, change_date)
                                   VALUES ($request_id, $current_quantity, $new_quantity, '$comment', NOW())";
                $conn->query($sql_log_change);

                echo "Quantity updated successfully.";
            } else {
                echo "Error updating quantity: " . $conn->error;
            }
        } else {
            echo "New quantity is the same as the current quantity. No update needed.";
        }
    } else {
        echo "Request not found.";
    }
} else {
    echo "Invalid request.";
}

$conn->close();
?>
