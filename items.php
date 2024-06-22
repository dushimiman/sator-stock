<?php
 include('includes/nav_bar.php'); 
$host = 'localhost';
$dbname = 'sock_management_system';
$username = 'root';
$password = '';

// Create a PDO instance
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Query to select all items
    $stmt = $pdo->query("SELECT * FROM items");
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View All Items</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <h2>View All Items</h2>
    <a href="addItem.php"><button>Add New Item</button></a>
    <br><br>
    <table>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Serial Number</th>
            <th>Quantity</th>
            <th>Created At</th>
            <th>Action</th>
        </tr>
        <?php foreach ($items as $item): ?>
        <tr>
            <td><?php echo htmlspecialchars($item['id']); ?></td>
            <td><?php echo htmlspecialchars($item['name']); ?></td>
            <td><?php echo htmlspecialchars($item['serial_number']); ?></td>
            <td><?php echo htmlspecialchars($item['quantity']); ?></td>
            <td><?php echo htmlspecialchars($item['created_at']); ?></td>
            <td>
                <a href="editItem.php?id=<?php echo $item['id']; ?>">Edit</a>
                <!-- You can add delete option here if required -->
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>