<?php
// Include the navigation bar
include('includes/nav_bar.php');

// Database credentials
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "stock_management_system";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch records from the out_in_stock table
$sql = "SELECT * FROM out_in_stock ORDER BY created_at DESC";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html>
<head>
    <title>View Out in Stock Items</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
    </style>
</head>
<body>
    <h2>View Out in Stock Items</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Item Name</th>
            <th>IMEI</th>
            <th>Serial Number</th>
            <th>Quantity</th>
            <th>Date Out</th>
        </tr>
        <?php
        if ($result->num_rows > 0) {
        
            while($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row["id"] . "</td>";
                echo "<td>" . $row["item_name"] . "</td>";
                echo "<td>" . $row["imei"] . "</td>";
                echo "<td>" . $row["serial_number"] . "</td>";
                echo "<td>" . $row["quantity"] . "</td>";
                echo "<td>" . $row["created_at"] . "</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='5'>No records found</td></tr>";
        }
        $conn->close();
        ?>
    </table>
</body>
</html>
