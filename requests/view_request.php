<?php
include('includes/db.php');

if (isset($_GET['id'])) {
    $request_id = $_GET['id'];
    $sql = "SELECT * FROM requests WHERE id = $request_id";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $request = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html>
<head>
    <title>View Request Details</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h2 class="mt-4 mb-4 text-center">Request Details</h2>
        <div class="table-responsive">
            <table class="table table-bordered">
                <tbody>
                    <tr>
                        <th style="width: 30%;">Requisition Date:</th>
                        <td><?php echo $request['requisition_date']; ?></td>
                    </tr>
                    
                    <tr>
                        <th>Requested By:</th>
                        <td><?php echo $request['requested_by']; ?></td>
                    </tr>
                    <tr>
                        <th>Item Name:</th>
                        <td><?php echo $request['item_name']; ?></td>
                    </tr>
                    <tr>
                        <th>Quantity Needed:</th>
                        <td><?php echo $request['quantity']; ?></td>
                    </tr>
                    <tr>
                        <th>Location </th>
                        <td><?php echo $request['location']; ?></td>
                    </tr>
                    <tr>
                        <th>Payment Description:</th>
                        <td><?php echo $request['payment_description']; ?></td>
                    </tr>
                    <tr>
                        <th>Reasons for Request:</th>
                        <td><?php echo $request['reasons']; ?></td>
                    </tr>
                    <tr>
                        <th>Status:</th>
                        <td><?php echo $request['status']; ?></td>
                    </tr>
                    <tr>
                        <th>Edit Quantity:</th>
                        <td>
                            <form action="edit_quantity.php" method="post">
                                <input type="hidden" name="request_id" value="<?php echo $request['id']; ?>">
                                <input type="number" name="new_quantity" value="<?php echo $request['quantity']; ?>" required>
                                <label for="comment">Comment:</label>
                                <textarea id="comment" name="comment" rows="2" cols="30"></textarea>
                                <button type="submit" class="btn btn-sm btn-primary">Update Quantity</button>
                            </form>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <br>
        <a href="request_list.php" class="btn btn-primary">Back to Requests List</a>
    </div>

    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

<?php
    } else {
        echo "Request not found.";
    }
} else {
    echo "Request ID not specified.";
}

$conn->close();
?>
