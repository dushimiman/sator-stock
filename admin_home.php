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

$sql = "SELECT item_type, SUM(quantity) AS total_quantity FROM stock GROUP BY item_type";
$result = $conn->query($sql);

$dataPoints = array();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $dataPoint = array("label" => $row['item_type'], "y" => $row['total_quantity']);
        array_push($dataPoints, $dataPoint);
    }
}

// Query to check for low stock items
$sql_low_stock_items = "SELECT item_type, SUM(quantity) AS total_quantity FROM stock GROUP BY item_type HAVING SUM(quantity) < 5";
$result_low_stock_items = $conn->query($sql_low_stock_items);

$sql_total_items_in_stock = "SELECT SUM(quantity) AS total_items_in_stock FROM stock";
$result_total_items_in_stock = $conn->query($sql_total_items_in_stock);

if ($result_total_items_in_stock->num_rows > 0) {
    $row = $result_total_items_in_stock->fetch_assoc();
    $total_items_in_stock = $row['total_items_in_stock'];
} else {
    $total_items_in_stock = 0; // Default to 0 if no records found
}

$sql_total_items_requested = "SELECT COUNT(*) AS total_items_requested FROM requests";
$result_total_items_requested = $conn->query($sql_total_items_requested);

if ($result_total_items_requested->num_rows > 0) {
    $row = $result_total_items_requested->fetch_assoc();
    $total_items_requested = $row['total_items_requested'];
} else {
    $total_items_requested = 0; // Default to 0 if no records found
}

$sql_total_items_out_of_stock = "SELECT COUNT(*) AS total_items_out_of_stock FROM out_in_stock";
$result_total_items_out_of_stock = $conn->query($sql_total_items_out_of_stock);

if ($result_total_items_out_of_stock->num_rows > 0) {
    $row = $result_total_items_out_of_stock->fetch_assoc();
    $total_items_out_of_stock = $row['total_items_out_of_stock'];
} else {
    $total_items_out_of_stock = 0; // Default to 0 if no records found
}

$sql_total_repaired_items = "SELECT COUNT(*) AS total_repaired_items FROM returned_items WHERE is_working = 1";
$result_total_repaired_items = $conn->query($sql_total_repaired_items);

if ($result_total_repaired_items->num_rows > 0) {
    $row = $result_total_repaired_items->fetch_assoc();
    $total_repaired_items = $row['total_repaired_items'];
} else {
    $total_repaired_items = 0; // Default to 0 if no records found
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock Management Dashboard</title>
    
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    
    <style>
        body {
            background-color: #f8f9fa;
            color: #343a40;
            padding: 20px;
            font-size: small; 
        }
        .top-margin {
            margin-top: 20px;
        }
        #itemChartContainer {
            max-width: 800px; 
            margin: 0 auto;
        }
        .modal-notification {
            position: fixed;
            top: 70px; 
            right: 20px;
            z-index: 1050; 
        }
        .navbar {
            margin-bottom: 20px;
        }
        .card {
            margin-bottom: 20px;
        }
        .card-title {
            font-size: medium; 
        }
        .icon-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
        }
        .icon-bar .icon {
            font-size: 24px;
            cursor: pointer;
        }
        .dropdown-menu {
            left: auto;
            right: 0;
        }
    </style>
</head>
<body>
    <div class="container-fluid top-margin">
        <div class="icon-bar">
            <div class="icon">
                <i class="fas fa-search"></i>
            </div>
            <div class="icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="icon">
                <i class="fas fa-bell"></i>
            </div>
            <div class="dropdown">
                <div class="icon dropdown-toggle" id="userDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-user"></i>
                </div>
                <div class="dropdown-menu" aria-labelledby="userDropdown">
                    <a class="dropdown-item" href="#">Logout</a>
                </div>
            </div>
        </div>

        <div class="row mb-4">
           
            <div class="col-md-3">
                <div class="card text-white bg-primary">
                    <div class="card-body">
                        <h5 class="card-title">Total Quantity in Stock</h5>
                        <p class="card-text"><?php echo $total_items_in_stock; ?></p>
                    </div>
                </div>
            </div>

            <!-- Total Items Requested Card -->
            <div class="col-md-3">
                <div class="card text-white bg-secondary">
                    <div class="card-body">
                        <h5 class="card-title">Total Items Requested</h5>
                        <p class="card-text"><?php echo $total_items_requested; ?></p>
                    </div>
                </div>
            </div>

            <!-- Total Items Out of Stock Card -->
            <div class="col-md-3">
                <div class="card text-white bg-danger">
                    <div class="card-body">
                        <h5 class="card-title">Total Items Out of Stock</h5>
                        <p class="card-text"><?php echo $total_items_out_of_stock; ?></p>
                    </div>
                </div>
            </div>

            <!-- Total Repaired Items Card -->
            <div class="col-md-3">
                <div class="card text-white bg-success">
                    <div class="card-body">
                        <h5 class="card-title">Total Repaired Items</h5>
                        <p class="card-text"><?php echo $total_repaired_items; ?></p>
                    </div>
                </div>
            </div>
        </div>

       
        <div class="row">
            <div class="col-lg-8 offset-lg-2">
               
                <div class="row mb-4">
                    <div class="col-md-8">
                        <div id="itemChartContainer">
                            <canvas id="itemChart"></canvas>
                        </div>
                    </div>
                    <div class="col-md-4">
                       
                        <div class="modal fade modal-notification" id="lowStockModal" tabindex="-1" role="dialog" aria-labelledby="lowStockModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="lowStockModalLabel">Low Stock Notification</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <?php if ($result_low_stock_items->num_rows > 0): ?>
                                            <p>Some items have low quantities in stock:</p>
                                            <ul>
                                                <?php while ($row = $result_low_stock_items->fetch_assoc()): ?>
                                                    <li><?php echo htmlspecialchars($row['item_type']) . ' (Total Quantity: ' . htmlspecialchars($row['total_quantity']) . ')'; ?></li>
                                                <?php endwhile; ?>
                                            </ul>
                                        <?php else: ?>
                                            <p>No items with low quantities in stock.</p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                       
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://kit.fontawesome.com/a076d05399.js"></script>
    
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var ctx = document.getElementById('itemChart').getContext('2d');
            var itemChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: <?php echo json_encode(array_column($dataPoints, 'label')); ?>,
                    datasets: [{
                        data: <?php echo json_encode(array_column($dataPoints, 'y')); ?>,
                        backgroundColor: ['#007bff', '#6c757d', '#dc3545', '#28a745', '#ffc107', '#17a2b8', '#fd7e14', '#343a40', '#6610f2'],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    legend: {
                        position: 'top',
                    },
                    // animation: {
                    //     animateScale: false,
                    //     animateRotate: false
                    // }
                }
            });

            
            $('#lowStockModal').modal('show');
        });
    </script>
</body>
</html>
