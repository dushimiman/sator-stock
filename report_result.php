<?php

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $startDate = $_POST['start_date'];
    $endDate = $_POST['end_date'];
    $reportType = $_POST['report_type'];

    $conn = new mysqli('localhost', 'root', '', 'stock_management_system');

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    function getItemsInStock($conn) {
        $query = "SELECT * FROM items";
        $result = $conn->query($query);
        return $result;
    }
    include('includes/nav_bar.php');
    function getEditedItems($conn, $startDate, $endDate) {
        $query = "SELECT * FROM items WHERE last_updated BETWEEN ? AND ?";
        $stmt = $conn->prepare($query);
        if (!$stmt) {
            die("Error preparing statement: " . $conn->error);
        }
        $stmt->bind_param('ss', $startDate, $endDate);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result;
    }

    function getRequestedItems($conn, $startDate, $endDate) {
        $query = "SELECT * FROM requisitions WHERE requisition_date BETWEEN ? AND ?";
        $stmt = $conn->prepare($query);
        if (!$stmt) {
            die("Error preparing statement: " . $conn->error);
        }
        $stmt->bind_param('ss', $startDate, $endDate);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result;
    }

    function getReturnedItems($conn, $startDate, $endDate) {
        $query = "SELECT * FROM returned_items WHERE returned_date BETWEEN ? AND ?";
        $stmt = $conn->prepare($query);
        if (!$stmt) {
            die("Error preparing statement: " . $conn->error);
        }
        $stmt->bind_param('ss', $startDate, $endDate);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result;
    }

    $itemsInStock = getItemsInStock($conn);
    $editedItems = getEditedItems($conn, $startDate, $endDate);
    $requestedItems = getRequestedItems($conn, $startDate, $endDate);
    $returnedItems = getReturnedItems($conn, $startDate, $endDate);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report Results</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">Report Results From <?= $startDate ?> To <?= $endDate ?></h2>

        <h3>Items In Stock</h3>
        <table class="table table-bordered table-hover">
            <thead class="thead-dark">
                <tr>
                    <th>ID</th>
                    <th>Item Name</th>
                    <th>Item Type</th>
                    <th>Serial Number</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $itemsInStock->fetch_assoc()) { ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= $row['name'] ?></td>
                    <td><?= $row['type'] ?></td>
                    <td><?= $row['serial_number'] ?></td>
                </tr>
                <?php } ?>
            </tbody>
        </table>

        <h3>Edited Items</h3>
        <table class="table table-bordered table-hover">
            <thead class="thead-dark">
                <tr>
                    <th>ID</th>
                    <th>Item Name</th>
                    <th>Item Type</th>
                    <th>Serial Number</th>
                    <th>Added By</th>
                    <th>Last Edited By</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $editedItems->fetch_assoc()) { ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= $row['item_name'] ?></td>
                    <td><?= $row['item_type'] ?></td>
                    <td><?= $row['serial_number'] ?></td>
                    <td><?= $row['added_by'] ?></td>
                    <td><?= $row['last_edited_by'] ?></td>
                </tr>
                <?php } ?>
            </tbody>
        </table>

        <h3>Requested Items</h3>
        <table class="table table-bordered table-hover">
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
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $requestedItems->fetch_assoc()) { ?>
                <tr>
                    <td><?= $row['requisition_date'] ?></td>
                    <td><?= $row['requisition_number'] ?></td>
                    <td><?= $row['requested_by'] ?></td>
                    <td><?= $row['requester_position'] ?></td>
                    <td><?= $row['approved_by'] ?></td>
                    <td><?= $row['approver_position'] ?></td>
                    <td><?= $row['serial_number'] ?></td>
                    <td><?= $row['item_name'] ?></td>
                    <td><?= $row['item_type'] ?></td>
                </tr>
                <?php } ?>
            </tbody>
        </table>

        <h3>Returned Items</h3>
        <table class="table table-bordered table-hover">
            <thead class="thead-dark">
                <tr>
                    <th>ID</th>
                    <th>Serial Number</th>
                    <th>Returned By</th>
                    <th>Received By</th>
                    <th>Return Reason</th>
                    <th>Returned Date</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $returnedItems->fetch_assoc()) { ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= $row['serial_number'] ?></td>
                    <td><?= $row['returned_by'] ?></td>
                    <td><?= $row['received_by'] ?></td>
                    <td><?= $row['return_reason'] ?></td>
                    <td><?= $row['returned_date'] ?></td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</body>
</html>

<?php
    $conn->close();
}
?>
