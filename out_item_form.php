<?php
include('includes/nav_bar.php');
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "stock_management_system";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT * FROM requests WHERE id = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        die("Error preparing the query: " . $conn->error);
    }

    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $request = $result->fetch_assoc();
    $stmt->close();
} else {
    die("No request ID provided.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $item_name = $_POST['item_name'];
    $quantity = $_POST['quantity'];
    $errors = [];

    $conn->begin_transaction();

    try {
        if ($item_name === 'GPS TRACKERS') {
            $imeis = array_map('trim', explode(',', $_POST['imei']));
            if (count($imeis) !== (int)$quantity) {
                $errors[] = "The number of IMEIs must match the requested quantity.";
            }

            foreach ($imeis as $imei) {
                $sql = "SELECT id, item_name, imei FROM stock WHERE item_name = 'GPS TRACKERS' AND LOWER(imei) = LOWER(?)
                        UNION
                        SELECT id, item_name, imei FROM returned_items WHERE item_name = 'GPS TRACKERS' AND LOWER(imei) = LOWER(?)";
                $stmt = $conn->prepare($sql);
                if ($stmt === false) {
                    throw new Exception("Error preparing the query: " . $conn->error);
                }
                $stmt->bind_param("ss", $imei, $imei);
                $stmt->execute();
                $result = $stmt->get_result();
                if ($result->num_rows === 0) {
                    $errors[] = "IMEI $imei is not available in stock or returned items.";
                } else {
                    $item = $result->fetch_assoc();
                
                    if ($item['id']) {
                        $deleteSql = "DELETE FROM stock WHERE imei = ? AND item_name = ? LIMIT 1";
                        $deleteStmt = $conn->prepare($deleteSql);
                        $deleteStmt->bind_param("ss", $imei, $item_name);
                        $deleteStmt->execute();
                        if ($deleteStmt->affected_rows === 0) {
                            $deleteSql = "DELETE FROM returned_items WHERE imei = ? AND item_name = ? LIMIT 1";
                            $deleteStmt = $conn->prepare($deleteSql);
                            $deleteStmt->bind_param("ss", $imei, $item_name);
                            $deleteStmt->execute();
                        }
                        $deleteStmt->close();
                    }
                    
                    $insertSql = "INSERT INTO out_in_stock (item_name, imei) VALUES (?, ?)";
                    $insertStmt = $conn->prepare($insertSql);
                    $insertStmt->bind_param("ss", $item_name, $imei);
                    $insertStmt->execute();
                    $insertStmt->close();
                }
                $stmt->close();
            }
        } elseif ($item_name === 'SPEED GOVERNORS') {
            $serial_numbers = array_map('trim', explode(',', $_POST['serial_number']));
            if (count($serial_numbers) !== (int)$quantity) {
                $errors[] = "The number of Serial Numbers must match the requested quantity.";
            }

            foreach ($serial_numbers as $serial_number) {
                $sql = "SELECT id, item_name, serial_number FROM stock WHERE item_name = 'SPEED GOVERNORS' AND LOWER(serial_number) = LOWER(?)
                        UNION
                        SELECT id, item_name, serial_number FROM returned_items WHERE item_name = 'SPEED GOVERNORS' AND LOWER(serial_number) = LOWER(?)";
                $stmt = $conn->prepare($sql);
                if ($stmt === false) {
                    throw new Exception("Error preparing the query: " . $conn->error);
                }
                $stmt->bind_param("ss", $serial_number, $serial_number);
                $stmt->execute();
                $result = $stmt->get_result();
                if ($result->num_rows === 0) {
                    $errors[] = "Serial Number $serial_number is not available in stock or returned items.";
                } else {
                    $item = $result->fetch_assoc();
                    
                    if ($item['id']) {
                        $deleteSql = "DELETE FROM stock WHERE serial_number = ? AND item_name = ? LIMIT 1";
                        $deleteStmt = $conn->prepare($deleteSql);
                        $deleteStmt->bind_param("ss", $serial_number, $item_name);
                        $deleteStmt->execute();
                        if ($deleteStmt->affected_rows === 0) {
                            $deleteSql = "DELETE FROM returned_items WHERE serial_number = ? AND item_name = ? LIMIT 1";
                            $deleteStmt = $conn->prepare($deleteSql);
                            $deleteStmt->bind_param("ss", $serial_number, $item_name);
                            $deleteStmt->execute();
                        }
                        $deleteStmt->close();
                    }
                   
                    $insertSql = "INSERT INTO out_in_stock (item_name, serial_number) VALUES (?, ?)";
                    $insertStmt = $conn->prepare($insertSql);
                    $insertStmt->bind_param("ss", $item_name, $serial_number);
                    $insertStmt->execute();
                    $insertStmt->close();
                }
                $stmt->close();
            }
        } else {
            // For items other than GPS TRACKERS and SPEED GOVERNORS
            $sql = "SELECT quantity FROM stock WHERE item_name = ?";
            $stmt = $conn->prepare($sql);
            if ($stmt === false) {
                throw new Exception("Error preparing the query: " . $conn->error);
            }
            $stmt->bind_param("s", $item_name);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 0) {
                $errors[] = "$item_name is not available in stock.";
            } else {
                $item = $result->fetch_assoc();
                if ($item['quantity'] < $quantity) {
                    $errors[] = "Not enough quantity of $item_name in stock.";
                } else {
                    $new_quantity = $item['quantity'] - $quantity;

                    $updateSql = "UPDATE stock SET quantity = ? WHERE item_name = ?";
                    $updateStmt = $conn->prepare($updateSql);
                    if ($updateStmt === false) {
                        throw new Exception("Error preparing the update query: " . $conn->error);
                    }
                    $updateStmt->bind_param("is", $new_quantity, $item_name);
                    $updateStmt->execute();
                    $updateStmt->close();

                    $insertSql = "INSERT INTO out_in_stock (item_name, quantity) VALUES (?, ?)";
                    $insertStmt = $conn->prepare($insertSql);
                    if ($insertStmt === false) {
                        throw new Exception("Error preparing the insert query: " . $conn->error);
                    }
                    $insertStmt->bind_param("si", $item_name, $quantity);
                    $insertStmt->execute();
                    $insertStmt->close();
                }
            }
            $stmt->close();
        }

        if (empty($errors)) {
            $conn->commit();
            echo "Item successfully outed in stock.";
        } else {
            $conn->rollback();
            foreach ($errors as $error) {
                echo "<p>$error</p>";
            }
        }
    } catch (Exception $e) {
        $conn->rollback();
        echo "<p>Error: " . $e->getMessage() . "</p>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Out Item in Stock</title>
</head>
<body>
    <h2>Out Item in Stock</h2>
    <form action="" method="post">
        <input type="hidden" name="request_id" value="<?php echo $request['id']; ?>">
        <label for="item_name">Item Name:</label>
        <input type="text" id="item_name" name="item_name" value="<?php echo $request['item_name']; ?>" readonly><br><br>
        <label for="quantity">Quantity:</label>
        <input type="number" id="quantity" name="quantity" value="<?php echo $request['quantity']; ?>" readonly><br><br>

        <?php if ($request['item_name'] === 'GPS TRACKERS'): ?>
        <div id="imei_section">
            <label for="imei">IMEI (comma separated):</label>
            <input type="text" id="imei" name="imei"><br><br>
        </div>
        <?php elseif ($request['item_name'] === 'SPEED GOVERNORS'): ?>
        <div id="serial_number_section">
            <label for="serial_number">Serial Number (comma separated):</label>
            <input type="text" id="serial_number" name="serial_number"><br><br>
        </div>
        <?php endif; ?>

        <button type="submit">Out Item</button>
    </form>
</body>
</html>

