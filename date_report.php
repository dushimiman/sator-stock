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


$date = isset($_POST['date']) ? $_POST['date'] : date("Y-m-d");


$sql_stock_by_date = "SELECT item_type, SUM(quantity) AS total_quantity 
                      FROM stock 
                      WHERE DATE(creation_date) = '$date' 
                      GROUP BY item_type";
$result_stock_by_date = $conn->query($sql_stock_by_date);


$sql_requests_by_date = "SELECT item_name, SUM(quantity) AS total_quantity, status 
                         FROM requests 
                         WHERE DATE(requisition_date) = '$date' 
                         GROUP BY item_name, status";
$result_requests_by_date = $conn->query($sql_requests_by_date);

$sql_return_date = "SELECT item_name, returned_by, return_reason 
                     FROM returned_items 
                     WHERE DATE(returned_date) = '$date'";
$result_return_date = $conn->query($sql_return_date);

if (!$result_return_date) {
    echo "Error fetching returned items: " . $conn->error;
}
$sql_out_in_stock_by_date = "SELECT item_name, SUM(quantity) AS total_quantity 
                             FROM out_in_stock 
                             WHERE DATE(out_date) = '$date' 
                             GROUP BY item_name";
$result_out_in_stock_by_date = $conn->query($sql_out_in_stock_by_date);


$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Stock Report - By Date</title>
    
  
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
       
        .container {
            margin-top: 20px;
        }
        table {
            width: 100%;
            margin-bottom: 20px;
        }
        table th, table td {
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="mt-4">Stock Report - By Date</h2>

        
        <form method="post">
            <div class="form-group">
                <label for="date">Select Date:</label>
                <input type="date" id="date" name="date" class="form-control" value="<?php echo $date; ?>">
            </div>
            <button type="submit" class="btn btn-primary">Generate Report</button>
        </form>

        <div class="mt-4">
            <h3>Items in Stock for <?php echo $date; ?></h3>
            <table class="table table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th>Item Type</th>
                        <th>Quantity</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                  
                    if ($result_stock_by_date && $result_stock_by_date->num_rows > 0) {
                        while($row = $result_stock_by_date->fetch_assoc()) {
                            echo "<tr><td>".$row["item_type"]."</td><td>".$row["total_quantity"]."</td></tr>";
                        }
                    } else {
                        echo "<tr><td colspan='2'>No items in stock found for this date.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            <h3>Requests for <?php echo $date; ?></h3>
            <table class="table table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th>Item Name</th>
                        <th>Quantity</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                 
                    if ($result_requests_by_date && $result_requests_by_date->num_rows > 0) {
                        while($row = $result_requests_by_date->fetch_assoc()) {
                            echo "<tr><td>".$row["item_name"]."</td><td>".$row["total_quantity"]."</td><td>".$row["status"]."</td></tr>";
                        }
                    } else {
                        echo "<tr><td colspan='3'>No requests found for this date.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            <h3>Returns item  for <?php echo $date; ?></h3>
            <table class="table table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th>Item Name</th>
                        <th>Returned By</th>
                        <th>Return Reason</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                 
                    if ($result_requests_by_date && $result_requests_by_date->num_rows > 0) {
                        while($row = $result_requests_by_date->fetch_assoc()) {
                            echo "<tr><td>".$row["item_name"]."</td><td>".$row["returned_by"]."</td><td>".$row["return_reason"]."</td></tr>";
                        }
                    } else {
                        echo "<tr><td colspan='3'>No return found for this date.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            <h3>Items Out in Stock for <?php echo $date; ?></h3>
            <table class="table table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th>Item Name</th>
                        <th>Quantity</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    
                    if ($result_out_in_stock_by_date && $result_out_in_stock_by_date->num_rows > 0) {
                        while($row = $result_out_in_stock_by_date->fetch_assoc()) {
                            echo "<tr><td>".$row["item_name"]."</td><td>".$row["total_quantity"]."</td></tr>";
                        }
                    } else {
                        echo "<tr><td colspan='2'>No items out in stock found for this date.</td></tr>";
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
