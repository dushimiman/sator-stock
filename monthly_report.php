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

$current_month = date("Y-m"); 


$sql_stock_monthly = "SELECT item_type, SUM(quantity) AS total_quantity 
                      FROM stock 
                      WHERE DATE_FORMAT(creation_date, '%Y-%m') = '$current_month' 
                      GROUP BY item_type";
$result_stock_monthly = $conn->query($sql_stock_monthly);


$sql_requests_monthly = "SELECT item_name, SUM(quantity) AS total_quantity, status 
                         FROM requests 
                         WHERE DATE_FORMAT(requisition_date, '%Y-%m') = '$current_month' 
                         GROUP BY item_name, status";
$result_requests_monthly = $conn->query($sql_requests_monthly);


$sql_out_in_stock_monthly = "SELECT item_name, SUM(quantity) AS total_quantity 
                             FROM out_in_stock 
                             WHERE DATE_FORMAT(created_at, '%Y-%m') = '$current_month' 
                             GROUP BY item_name";
$result_out_in_stock_monthly = $conn->query($sql_out_in_stock_monthly);


$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock Report - Monthly (<?php echo $current_month; ?>)</title>
    
    
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
       
        body {
            padding: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h4 class="mt-4">Stock Report - Monthly (<?php echo $current_month; ?>)</h4>

        <div class="mt-4">
            <h3>Items in Stock</h3>
            <table class="table table-striped table-responsive-md">
                <thead class="thead-dark">
                    <tr>
                        <th>Item Type</th>
                        <th>Quantity</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                   
                    if ($result_stock_monthly && $result_stock_monthly->num_rows > 0) {
                        while($row = $result_stock_monthly->fetch_assoc()) {
                            echo "<tr><td>".$row["item_type"]."</td><td>".$row["total_quantity"]."</td></tr>";
                        }
                    } else {
                        echo "<tr><td colspan='2'>No items in stock found for this month.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            <h3>Requests</h3>
            <table class="table table-striped table-responsive-md">
                <thead class="thead-dark">
                    <tr>
                        <th>Item Name</th>
                        <th>Quantity</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    
                    if ($result_requests_monthly && $result_requests_monthly->num_rows > 0) {
                        while($row = $result_requests_monthly->fetch_assoc()) {
                            echo "<tr><td>".$row["item_name"]."</td><td>".$row["total_quantity"]."</td><td>".$row["status"]."</td></tr>";
                        }
                    } else {
                        echo "<tr><td colspan='3'>No requests found for this month.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            <h3>Items Out in Stock</h3>
            <table class="table table-striped table-responsive-md">
                <thead class="thead-dark">
                    <tr>
                        <th>Item Name</th>
                        <th>Quantity</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                
                    if ($result_out_in_stock_monthly && $result_out_in_stock_monthly->num_rows > 0) {
                        while($row = $result_out_in_stock_monthly->fetch_assoc()) {
                            echo "<tr><td>".$row["item_name"]."</td><td>".$row["total_quantity"]."</td></tr>";
                        }
                    } else {
                        echo "<tr><td colspan='2'>No items out in stock found for this month.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            <a href="generate_pdf_month.php?start_date=<?php echo $start_date; ?>&end_date=<?php echo $end_date; ?>" target="_blank" class="btn btn-primary">Generate PDF Report</a>
        </div>
    </div>

    
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>
</body>
</html>
