<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "stock_management_system";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $request_id = $_POST['request_id'];
    $item_name = $_POST['item_name'];

    // Fetch requested quantity from the requests table
    $sql_request = "SELECT quantity FROM requests WHERE id = ?";
    $stmt_request = $conn->prepare($sql_request);
    if (!$stmt_request) {
        die("Error preparing the query: " . $conn->error);
    }
    $stmt_request->bind_param("i", $request_id);
    $stmt_request->execute();
    $stmt_request->bind_result($requested_quantity);
    $stmt_request->fetch();
    $stmt_request->close();

    if ($item_name === 'SPEED GOVERNORS' || $item_name === 'GPS TRACKERS') {
        // Check if serial numbers were provided
        if (isset($_POST['serial_numbers']) && !empty($_POST['serial_numbers'])) {
            $serial_numbers = explode(",", $_POST['serial_numbers']);
        } else {
            die("Serial numbers are required for SPEED GOVERNORS and GPS TRACKERS.");
        }

        // Check total quantity of serial numbers in stock
        $serial_count = count($serial_numbers);
        if ($serial_count < $requested_quantity) {
            die("The number of serial numbers provided does not match the requested quantity.");
        }

        $placeholders = implode(',', array_fill(0, $serial_count, '?'));
        $types = str_repeat('s', $serial_count);
        $sql_stock_serials = "SELECT COUNT(*) FROM stock WHERE item_name = ? AND serial_number IN ($placeholders)";
        $stmt_stock_serials = $conn->prepare($sql_stock_serials);
        if (!$stmt_stock_serials) {
            die("Error preparing the query: " . $conn->error);
        }
        $stmt_stock_serials->bind_param("s" . $types, $item_name, ...$serial_numbers);
        $stmt_stock_serials->execute();
        $stmt_stock_serials->bind_result($serials_in_stock);
        $stmt_stock_serials->fetch();
        $stmt_stock_serials->close();

        if ($serials_in_stock >= $requested_quantity) {
            // Delete items from stock table and insert into out_in_stock table
            foreach ($serial_numbers as $serial_number) {
                // Delete from stock table
                $sql_delete_stock = "DELETE FROM stock WHERE item_name = ? AND serial_number = ?";
                $stmt_delete_stock = $conn->prepare($sql_delete_stock);
                if (!$stmt_delete_stock) {
                    die("Error preparing the query: " . $conn->error);
                }
                $stmt_delete_stock->bind_param("ss", $item_name, $serial_number);
                $stmt_delete_stock->execute();
                $stmt_delete_stock->close();

                // Insert into out_in_stock table
                $sql_insert_out_stock = "INSERT INTO out_in_stock (item_name, serial_number, quantity) VALUES (?, ?, 1)";
                $stmt_insert_out_stock = $conn->prepare($sql_insert_out_stock);
                if (!$stmt_insert_out_stock) {
                    die("Error preparing the query: " . $conn->error);
                }
                $stmt_insert_out_stock->bind_param("ss", $item_name, $serial_number);
                $stmt_insert_out_stock->execute();
                $stmt_insert_out_stock->close();
            }

            // Mark request as processed
            $sql_update_request = "UPDATE requests SET status = 'processed' WHERE id = ?";
            $stmt_update_request = $conn->prepare($sql_update_request);
            if (!$stmt_update_request) {
                die("Error preparing the query: " . $conn->error);
            }
            $stmt_update_request->bind_param("i", $request_id);
            $stmt_update_request->execute();
            $stmt_update_request->close();

            echo "Item successfully taken out from stock.";
        } else {
            echo "Insufficient stock.";
        }
    } else {
        // Fetch stock quantity for items without serial numbers
        $sql_stock = "SELECT quantity FROM stock WHERE item_name = ?";
        $stmt_stock = $conn->prepare($sql_stock);
        if (!$stmt_stock) {
            die("Error preparing the query: " . $conn->error);
        }
        $stmt_stock->bind_param("s", $item_name);
        $stmt_stock->execute();
        $stmt_stock->bind_result($stock_quantity);
        $stmt_stock->fetch();
        $stmt_stock->close();

        if ($stock_quantity >= $requested_quantity) {
            // Update stock quantity
            $new_stock_quantity = $stock_quantity - $requested_quantity;
            $sql_update_stock = "UPDATE stock SET quantity = ? WHERE item_name = ?";
            $stmt_update_stock = $conn->prepare($sql_update_stock);
            if (!$stmt_update_stock) {
                die("Error preparing the query: " . $conn->error);
            }
            $stmt_update_stock->bind_param("is", $new_stock_quantity, $item_name);
            $stmt_update_stock->execute();
            $stmt_update_stock->close();

            // Insert into out_in_stock table
            $sql_insert_out_stock = "INSERT INTO out_in_stock (item_name, quantity) VALUES (?, ?)";
            $stmt_insert_out_stock = $conn->prepare($sql_insert_out_stock);
            if (!$stmt_insert_out_stock) {
                die("Error preparing the query: " . $conn->error);
            }
            $stmt_insert_out_stock->bind_param("si", $item_name, $requested_quantity);
            $stmt_insert_out_stock->execute();
            $stmt_insert_out_stock->close();

            // Mark request as processed
            $sql_update_request = "UPDATE requests SET status = 'processed' WHERE id = ?";
            $stmt_update_request = $conn->prepare($sql_update_request);
            if (!$stmt_update_request) {
                die("Error preparing the query: " . $conn->error);
            }
            $stmt_update_request->bind_param("i", $request_id);
            $stmt_update_request->execute();
            $stmt_update_request->close();

            echo "Item successfully taken out from stock.";
        } else {
            echo "Insufficient stock.";
        }
    }
}

$conn->close();
?>
