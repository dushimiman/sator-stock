<?php
include('includes/nav_bar.php');
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "stock_management_system";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$current_date = date("Y-m-d");


$sql_stock_daily = "SELECT item_type, SUM(quantity) AS total_quantity 
                    FROM stock 
                    WHERE DATE(creation_date) = '$current_date' 
                    GROUP BY item_type";
$result_stock_daily = $conn->query($sql_stock_daily);

if (!$result_stock_daily) {
    echo "Error fetching items in stock: " . $conn->error;
}


$sql_requests_daily = "SELECT item_name, SUM(quantity) AS total_quantity, status 
                       FROM requests 
                       WHERE DATE(requisition_date) = '$current_date' 
                       GROUP BY item_name, status";
$result_requests_daily = $conn->query($sql_requests_daily);

if (!$result_requests_daily) {
    echo "Error fetching requests: " . $conn->error;
}


$sql_return_daily = "SELECT item_name, returned_by, return_reason 
                     FROM returned_items 
                     WHERE DATE(returned_date) = '$current_date'";
$result_return_daily = $conn->query($sql_return_daily);

if (!$result_return_daily) {
    echo "Error fetching returned items: " . $conn->error;
}


$sql_out_in_stock_daily = "SELECT item_name, SUM(quantity) AS total_quantity 
                           FROM out_in_stock 
                           WHERE DATE(out_date) = '$current_date' 
                           GROUP BY item_name";
$result_out_in_stock_daily = $conn->query($sql_out_in_stock_daily);

if (!$result_out_in_stock_daily) {
    echo "Error fetching items out in stock: " . $conn->error;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Stock Report - Daily (<?php echo $current_date; ?>)</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" rel="stylesheet">
    <style>
    </style>
</head>
<body>
    <div class="container">
        <h2 class="mt-4">Stock Report - Daily (<?php echo $current_date; ?>)</h2>

        <div class="mt-4">
            <h3>Items in Stock</h3>
            <table class="table table-striped table-bordered">
                <thead class="thead-dark">
                    <tr>
                        <th>Item Type</th>
                        <th>Quantity</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result_stock_daily && $result_stock_daily->num_rows > 0) {
                        while($row = $result_stock_daily->fetch_assoc()) {
                            echo "<tr><td>".$row["item_type"]."</td><td>".$row["total_quantity"]."</td></tr>";
                        }
                    } else {
                        echo "<tr><td colspan='2'>No items in stock found for today.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            <h3>Requests for Today</h3>
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
                    if ($result_requests_daily && $result_requests_daily->num_rows > 0) {
                        while($row = $result_requests_daily->fetch_assoc()) {
                            echo "<tr><td>".$row["item_name"]."</td><td>".$row["total_quantity"]."</td><td>".$row["status"]."</td></tr>";
                        }
                    } else {
                        echo "<tr><td colspan='3'>No requests found for today.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            <h3>Items Returned for Today</h3>
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
                    if ($result_return_daily && $result_return_daily->num_rows > 0) {
                        while($row = $result_return_daily->fetch_assoc()) {
                            echo "<tr><td>".$row["item_name"]."</td><td>".$row["returned_by"]."</td><td>".$row["return_reason"]."</td></tr>";
                        }
                    } else {
                        echo "<tr><td colspan='3'>No items returned found for today.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            <h3>Items Out in Stock for Today</h3>
            <table class="table table-striped table-bordered">
                <thead class="thead-dark">
                    <tr>
                        <th>Item Name</th>
                        <th>Quantity</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result_out_in_stock_daily && $result_out_in_stock_daily->num_rows > 0) {
                        while($row = $result_out_in_stock_daily->fetch_assoc()) {
                            echo "<tr><td>".$row["item_name"]."</td><td>".$row["total_quantity"]."</td></tr>";
                        }
                    } else {
                        echo "<tr><td colspan='2'>No items out in stock found for today.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>
</body>
</html>
