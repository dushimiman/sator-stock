<?php
session_start();
include(__DIR__ . '/../includes/nav_bar.php');
include(__DIR__ . '/../includes/db.php'); 
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    header("Location: ../login.php"); 
    exit();
}
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['request_id']) && isset($_POST['new_quantity'])) {
    $request_id = $_POST['request_id'];
    $new_quantity = $_POST['new_quantity'];
    $comment = $_POST['comment'];

    // Fetch the current request details
    $sql_fetch_request = "SELECT * FROM requests WHERE id = ?";
    $stmt_fetch = $mysqli->prepare($sql_fetch_request);
    
    if (!$stmt_fetch) {
        die("Error preparing fetch request query: " . $mysqli->error);
    }

    $stmt_fetch->bind_param("i", $request_id);
    $stmt_fetch->execute();
    $result_fetch_request = $stmt_fetch->get_result();

    if ($result_fetch_request->num_rows == 1) {
        $request = $result_fetch_request->fetch_assoc();
        $current_quantity = $request['quantity'];
        $item_id = $request['item_id'];  // Retrieve item_id

        // Check if quantity needs to be updated
        if ($new_quantity != $current_quantity) {
            // Update the quantity and append the comment to the reasons field
            $sql_update_quantity = "UPDATE requests 
                                    SET quantity = ?, 
                                        reasons = CONCAT(COALESCE(reasons, ''), ' ', ?) 
                                    WHERE id = ?";
            $stmt_update = $mysqli->prepare($sql_update_quantity);
            
            if (!$stmt_update) {
                die("Error preparing update quantity query: " . $mysqli->error);
            }

            $comment = $mysqli->real_escape_string($comment);
            $stmt_update->bind_param('isi', $new_quantity, $comment, $request_id);

            if ($stmt_update->execute()) {
                // Log the quantity change
                $change_type = $new_quantity > $current_quantity ? 'ADD' : 'REMOVE';
                $change_quantity = abs($new_quantity - $current_quantity);

                $sql_log_change = "INSERT INTO quantity_change_log (request_id, item_id, change_type, change_quantity, comment, change_date)
                                   VALUES (?, ?, ?, ?, ?, NOW())";
                $stmt_log = $mysqli->prepare($sql_log_change);

                if (!$stmt_log) {
                    die("Error preparing log change query: " . $mysqli->error);
                }

                $stmt_log->bind_param('iisiss', $request_id, $item_id, $change_type, $change_quantity, $comment);
                $stmt_log->execute();

                header("Location: view_request.php?id=$request_id");
                exit();
            } else {
                echo "Error updating quantity: " . $mysqli->error;
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

$mysqli->close();
?>
