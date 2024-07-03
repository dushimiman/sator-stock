<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "stock_management_system";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


function getRequestDetails($conn, $request_id) {
    $sql = "SELECT * FROM requests WHERE id = $request_id";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        return $row;
    } else {
        return null;
    }
}


if (isset($_GET['id'])) {
    $request_id = $_GET['id'];
    $request = getRequestDetails($conn, $request_id);

    if (!$request) {
        die("Request not found.");
    }
} else {
    die("Request ID not specified.");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Request Details</title>
    <style>
        .details-table {
            width: 50%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .details-table th, .details-table td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
        }
        .details-table th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <h2>Request Details</h2>
    <table class="details-table">
        <tr>
            <th>Field</th>
            <th>Details</th>
        </tr>
        <tr>
            <td>Requisition Date</td>
            <td><?php echo $request['requisition_date']; ?></td>
        </tr>
        <tr>
            <td>Requisition Number</td>
            <td><?php echo $request['requisition_number']; ?></td>
        </tr>
        <tr>
            <td>Requested By</td>
            <td><?php echo $request['requested_by']; ?></td>
        </tr>
        <tr>
            <td>Item Name</td>
            <td><?php echo $request['item_name']; ?></td>
        </tr>
        <tr>
            <td>Payment Method</td>
            <td><?php echo $request['payment_method']; ?></td>
        </tr>
        <tr>
            <td>Status</td>
            <td><?php echo $request['status']; ?></td>
        </tr>
        <?php if ($request['payment_method'] !== 'none') { ?>
            <tr>
    <td>Proof of Payment</td>
    <td>
        <?php
        if (!empty($request['proof_of_payment_file'])) {
            $proof_file_path = 'proofs/' . $request['proof_of_payment_file'];
            echo "<a href='$proof_file_path' download>Download Proof of Payment</a>";
        } else {
            echo "Proof of Payment Not Provided";
        }
        ?>
    </td>
</tr>

        </tr>
        <?php } else { ?>
        <tr>
            <td>Reason if Not Paid</td>
            <td><?php echo $request['reason_if_not_paid']; ?></td>
        </tr>
        <?php } ?>
    </table>
    <br>
    <a href="request_list.php">Back to Requests List</a>
</body>
</html>

<?php
$conn->close();
?>
