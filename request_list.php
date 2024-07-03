<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "stock_management_system";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch requests
$sql = "SELECT * FROM requests";
$result = $conn->query($sql);

if ($result === false) {
    die("Error executing the query: " . $conn->error);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Requests List</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <h2>Requests List</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Requisition Date</th>
            <th>Requisition Number</th>
            <th>Requested By</th>
            <th>Item Name</th>
            <th>Payment Method</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row['id'] . "</td>";
                echo "<td>" . $row['requisition_date'] . "</td>";
                echo "<td>" . $row['requisition_number'] . "</td>";
                echo "<td>" . $row['requested_by'] . "</td>";
                echo "<td>" . $row['item_name'] . "</td>";
                echo "<td>" . $row['payment_method'] . "</td>";
                echo "<td>" . $row['status'] . "</td>";
                echo "<td>";
                echo "<a href='approve_request.php?id=" . $row['id'] . "'>Approve</a> | ";
                echo "<a href='view_request.php?id=" . $row['id'] . "'>View Details</a>";
                echo "</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='8'>No requests found</td></tr>";
        }
        ?>
    </table>
</body>
</html>

<?php
$conn->close();
?>
