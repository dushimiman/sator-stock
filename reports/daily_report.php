<?php
session_start();
include(__DIR__ . '/../includes/nav_bar.php');
include(__DIR__ . '/../includes/db.php'); 
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    header("Location: ../login.php"); 
    exit();
}

$start_date = isset($_POST['start_date']) ? $_POST['start_date'] : '';
$end_date = isset($_POST['end_date']) ? $_POST['end_date'] : '';

$sql_stock = "SELECT item_type, SUM(quantity) AS total_quantity 
              FROM stock 
              WHERE DATE(creation_date) BETWEEN '$start_date' AND '$end_date' 
              GROUP BY item_type";
$result_stock = $mysqli->query($sql_stock);

$sql_requests = "SELECT item_name, SUM(quantity) AS total_quantity, status 
                 FROM requests 
                 WHERE DATE(requisition_date) BETWEEN '$start_date' AND '$end_date' 
                 GROUP BY item_name, status";
$result_requests = $mysqli->query($sql_requests);

$sql_return = "SELECT item_name, returned_by, return_reason 
               FROM returned_items 
               WHERE DATE(creation_date) BETWEEN '$start_date' AND '$end_date'";
$result_return = $mysqli->query($sql_return);

$sql_out_in_stock = "SELECT item_name, SUM(quantity) AS total_quantity 
                     FROM out_in_stock 
                     WHERE DATE(created_at) BETWEEN '$start_date' AND '$end_date' 
                     GROUP BY item_name";
$result_out_in_stock = $mysqli->query($sql_out_in_stock);

$sql_all_stock = "SELECT item_name, item_type, SUM(quantity) AS total_quantity 
                  FROM stock 
                  GROUP BY item_name, item_type";
$result_all_stock = $mysqli->query($sql_all_stock);

$mysqli->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Stock Report</title>
    <link rel="icon" href="./images/stock-icon.png" type="image/x-icon"> 
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h5 class="mt-4">Stock Report</h5>

        <form method="post" class="mt-4">
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="start_date">Start Date</label>
                    <input type="date" class="form-control" id="start_date" name="start_date" value="<?php echo $start_date; ?>" required>
                </div>
                <div class="form-group col-md-4">
                    <label for="end_date">End Date</label>
                    <input type="date" class="form-control" id="end_date" name="end_date" value="<?php echo $end_date; ?>" required>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Generate Report</button>
        </form>

        <div class="mt-4">
            <h6>All Items in Stock</h6>
            <table class="table table-striped table-bordered">
                <thead class="thead-dark">
                    <tr>
                        <th>Item Type</th>
                        <th>Quantity</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result_all_stock && $result_all_stock->num_rows > 0) {
                        while($row = $result_all_stock->fetch_assoc()) {
                            echo "<tr><td>".$row["item_type"]."</td><td>".$row["total_quantity"]."</td></tr>";
                        }
                    } else {
                        echo "<tr><td colspan='3'>No items in stock found.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <!-- Display items in stock within date range -->
        <div class="mt-4">
            <h6>Items added in Stock (<?php echo $start_date . " to " . $end_date; ?>)</h6>
            <table class="table table-striped table-bordered">
                <thead class="thead-dark">
                    <tr>
                        <th>Item Type</th>
                        <th>Quantity</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result_stock && $result_stock->num_rows > 0) {
                        while($row = $result_stock->fetch_assoc()) {
                            echo "<tr><td>".$row["item_type"]."</td><td>".$row["total_quantity"]."</td></tr>";
                        }
                    } else {
                        echo "<tr><td colspan='2'>No items in stock found for the selected date range.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <!-- Display requests for the date range -->
        <div class="mt-4">
            <h6>Requests</h6>
            <table class="table table-striped table-bordered">
                <thead class="thead-dark">
                    <tr>
                        <th>Item Name</th>
                        <th>Quantity</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result_requests && $result_requests->num_rows > 0) {
                        while($row = $result_requests->fetch_assoc()) {
                            echo "<tr><td>".$row["item_name"]."</td><td>".$row["total_quantity"]."</td><td>".$row["status"]."</td></tr>";
                        }
                    } else {
                        echo "<tr><td colspan='3'>No requests found for the selected date range.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <!-- Display items returned for the date range -->
        <div class="mt-4">
            <h6>Items Returned</h6>
            <table class="table table-striped table-bordered">
                <thead class="thead-dark">
                    <tr>
                        <th>Item Name</th>
                        <th>Returned By</th>
                        <th>Return Reason</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result_return && $result_return->num_rows > 0) {
                        while($row = $result_return->fetch_assoc()) {
                            echo "<tr><td>".$row["item_name"]."</td><td>".$row["returned_by"]."</td><td>".$row["return_reason"]."</td></tr>";
                        }
                    } else {
                        echo "<tr><td colspan='3'>No items returned found for the selected date range.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <!-- Display items out in stock for the date range -->
        <div class="mt-4">
            <h6>Items Out in Stock</h6>
            <table class="table table-striped table-bordered">
                <thead class="thead-dark">
                    <tr>
                        <th>Item Name</th>
                        <th>Quantity</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result_out_in_stock && $result_out_in_stock->num_rows > 0) {
                        while($row = $result_out_in_stock->fetch_assoc()) {
                            echo "<tr><td>".$row["item_name"]."</td><td>".$row["total_quantity"]."</td></tr>";
                        }
                    } else {
                        echo "<tr><td colspan='2'>No items out in stock found for the selected date range.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <!-- Link to generate PDF report -->
        <div class="mt-4">
            <a href="generate_pdf.php?start_date=<?php echo $start_date; ?>&end_date=<?php echo $end_date; ?>" target="_blank" class="btn btn-primary">Generate PDF Report</a>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>
</body>
</html>
