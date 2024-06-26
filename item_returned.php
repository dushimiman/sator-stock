<?php
include('includes/nav_bar.php');
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "sock_management_system";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$return_items_query = "SELECT rt.id, i.name AS item_name, i.serial_number, rt.returned_by, rt.reason, rt.returned_at
                      FROM return_transactions rt
                      INNER JOIN items i ON rt.item_id = i.id
                      ORDER BY rt.returned_at DESC"; 

$result = $conn->query($return_items_query);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Return Items</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h2>Return Items</h2>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Item Name</th>
                        <th>Serial Number</th>
                        <th>Returned By</th>
                        <th>Reason</th>
                        <th>Returned At</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['id']); ?></td>
                                <td><?php echo htmlspecialchars($row['item_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['serial_number']); ?></td>
                                <td><?php echo htmlspecialchars($row['returned_by']); ?></td>
                                <td><?php echo htmlspecialchars($row['reason']); ?></td>
                                <td><?php echo htmlspecialchars($row['returned_at']); ?></td>
                            </tr>
                            <?php
                        }
                    } else {
                        ?>
                        <tr>
                            <td colspan="6">No return items found.</td>
                        </tr>
                        <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

<?php
$conn->close();
?>
