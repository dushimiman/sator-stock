<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "stock_management_system";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$query = "SELECT * FROM requisitions";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View All Requisitions</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .table-responsive {
            margin-top: 20px;
        }
        .table th, .table td {
            white-space: nowrap;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">All Requisitions</h2>
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th>Requisition Date</th>
                        <th>Requisition Number</th>
                        <th>Requested By</th>
                        <th>Requester Position</th>
                        <th>Approved By</th>
                        <th>Approver Position</th>
                        <th>Serial Number</th>
                        <th>Item Name</th>
                        <th>Item Type</th>
                        <th>Payment Method</th>
                        <th>Payment Reason</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . $row['requisition_date'] . "</td>";
                            echo "<td>" . $row['requisition_number'] . "</td>";
                            echo "<td>" . $row['requested_by'] . "</td>";
                            echo "<td>" . $row['requester_position'] . "</td>";
                            echo "<td>" . $row['approved_by'] . "</td>";
                            echo "<td>" . $row['approver_position'] . "</td>";
                            echo "<td>" . $row['serial_number'] . "</td>";
                            echo "<td>" . $row['item_name'] . "</td>";
                            echo "<td>" . $row['item_type'] . "</td>";
                            echo "<td>" . $row['payment_method'] . "</td>";
                            echo "<td>" . $row['payment_reason'] . "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='11' class='text-center'>No requisitions found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>

<?php
$conn->close();
?>
