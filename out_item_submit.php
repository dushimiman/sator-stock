


<?php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "stock_management_system";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$request_id = $_POST['request_id'];
$item_name = $_POST['item_name'];
$serial_number = $_POST['serial_number'] ?? null;
$quantity = $_POST['quantity'] ?? null;
$branch = $_POST['branch'] ?? null;

if ($item_name === "GPS TRACKERS" || $item_name === "SPEED GOVERNOR") {
    $sql = "SELECT * FROM stock WHERE item_name = ? AND serial_number = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        die("Error preparing the query: " . $conn->error);
    }

    $stmt->bind_param("ss", $item_name, $serial_number);
    $stmt->execute();
    $result = $stmt->get_result();
    $item = $result->fetch_assoc();
    $stmt->close();

    if ($item) {
        $sql = "DELETE FROM stock WHERE id = ?";
        $stmt = $conn->prepare($sql);

        if ($stmt === false) {
            die("Error preparing the query: " . $conn->error);
        }

        $stmt->bind_param("i", $item['id']);
        $stmt->execute();
        $stmt->close();

        $sql = "INSERT INTO out_of_stock (request_id, item_name, serial_number, quantity, branch, out_date) VALUES (?, ?, ?, ?, ?, CURRENT_TIMESTAMP)";
        $stmt = $conn->prepare($sql);

        if ($stmt === false) {
            die("Error preparing the insert query: " . $conn->error);
        }

        $stmt->bind_param("issis", $request_id, $item_name, $serial_number, $quantity, $branch);
        $stmt->execute();
        $stmt->close();
    } else {
        die("Item not found in stock.");
    }
} else {
    $sql = "SELECT * FROM stock WHERE item_name = ? AND branch = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        die("Error preparing the query: " . $conn->error);
    }

    $stmt->bind_param("ss", $item_name, $branch);
    $stmt->execute();
    $result = $stmt->get_result();
    $item = $result->fetch_assoc();
    $stmt->close();

    if ($item && $item['quantity'] >= $quantity) {
        $new_quantity = $item['quantity'] - $quantity;
        $sql = "UPDATE stock SET quantity = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);

        if ($stmt === false) {
            die("Error preparing the update query: " . $conn->error);
        }

        $stmt->bind_param("ii", $new_quantity, $item['id']);
        $stmt->execute();
        $stmt->close();

        $sql = "INSERT INTO out_of_stock (request_id, item_name, serial_number, quantity, branch, out_date) VALUES (?, ?, ?, ?, ?, CURRENT_TIMESTAMP)";
        $stmt = $conn->prepare($sql);

        if ($stmt === false) {
            die("Error preparing the insert query: " . $conn->error);
        }

        $stmt->bind_param("issis", $request_id, $item_name, $serial_number, $quantity, $branch);
        $stmt->execute();
        $stmt->close();
    } else {
        die("Insufficient stock.");
    }
}

echo "Item successfully marked as out of stock.";

$conn->close();

header("Location: approved_requests.php");
exit;
?>
