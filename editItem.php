<?php

session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "sock_management_system";


$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// checkRole('stock_manager');
$item_id = $_GET['id'];
$query = "SELECT * FROM items WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $item_id);
$stmt->execute();
$item = $stmt->get_result()->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $serial_number = $_POST['serial_number'];
    $quantity = $_POST['quantity'];

    $update_query = "UPDATE items SET name = ?, serial_number = ?, quantity = ? WHERE id = ?";
    $update_stmt = $conn->prepare($update_query);
    $update_stmt->bind_param('ssii', $name, $serial_number, $quantity, $item_id);
    $update_stmt->execute();

    header('Location: items.php');
}
?>

<form method="POST">
    Name: <input type="text" name="name" value="<?php echo $item['name']; ?>" required><br>
    Serial Number: <input type="text" name="serial_number" value="<?php echo $item['serial_number']; ?>" required><br>
    Quantity: <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" required><br>
    <button type="submit">Update Item</button>
</form>
