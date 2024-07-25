<?php
include('includes/db.php');

$sql = "SELECT * FROM requests WHERE status = 'approved'";
$result = $conn->query($sql);

if ($result === false) {
    die("Error executing the query: " . $conn->error);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Approved Requests</title>
</head>
<body>
    <h2>Approved Requests</h2>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Requisition Date</th>
            <th>Requisition Number</th>
            <th>Requested By</th>
            <th>Item Name</th>
            <th>Payment Method</th>
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
                echo "<td><a href='out_item_form.php?id=" . $row['id'] . "'>Out Item</a></td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='7'>No approved requests found</td></tr>";
        }
        ?>
    </table>
</body>
</html>

<?php
$conn->close();
?>
