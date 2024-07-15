<?php
include('includes/nav_bar.php');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "stock_management_system";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query to fetch data for doughnut chart (item_type and quantity)
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

// Query to fetch total items requested (assuming requests table structure)
$sql_total_items_requested = "SELECT COUNT(*) AS total_items_requested FROM requests";
$result_total_items_requested = $conn->query($sql_total_items_requested);

if ($result_total_items_requested->num_rows > 0) {
    $row = $result_total_items_requested->fetch_assoc();
    $total_items_requested = $row['total_items_requested'];
} else {
    $total_items_requested = 0; // Default to 0 if no records found
}

// Query to fetch total items out of stock (assuming out_in_stock table structure)
$sql_total_items_out_of_stock = "SELECT COUNT(*) AS total_items_out_of_stock FROM out_in_stock";
$result_total_items_out_of_stock = $conn->query($sql_total_items_out_of_stock);

if ($result_total_items_out_of_stock->num_rows > 0) {
    $row = $result_total_items_out_of_stock->fetch_assoc();
    $total_items_out_of_stock = $row['total_items_out_of_stock'];
} else {
    $total_items_out_of_stock = 0; // Default to 0 if no records found
}

// Query to fetch total repaired items (assuming returned_items table structure)
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
            font-size: small; /* Set default font size to small */
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
            animation: scale-in 0.5s ease; /* Add animation to cards */
            margin-bottom: 20px;
        }
        @keyframes scale-in {
            0% {
                transform: scale(0.9);
            }
            100% {
                transform: scale(1);
            }
        }
        .card-title {
            font-size: medium; /* Set font size of card titles */
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row mb-4">
            <!-- Total Quantity Card -->
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

        <!-- Doughnut Chart and Low Stock Modal -->
        <div class="row">
            <div class="col-lg-8 offset-lg-2">
                <!-- <h1 class="mt-4 mb-4 text-center">Stock Management Dashboard</h1> -->
                <div class="row mb-4">
                    <div class="col-md-8">
                        <div id="itemChartContainer">
                            <canvas id="itemChart"></canvas>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <!-- Notification Modal for Low Stock Items -->
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
                                            <p>No items have low quantities in stock.</p>
                                        <?php endif; ?>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Doughnut Chart Data
        var dataPoints = <?php echo json_encode($dataPoints, JSON_NUMERIC_CHECK); ?>;
        
        var ctx = document.getElementById('itemChart').getContext('2d');
        var itemChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: dataPoints.map(dp => dp.label),
                datasets: [{
                    label: 'Item Quantities',
                    data: dataPoints.map(dp => dp.y),
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.6)',
                        'rgba(54, 162, 235, 0.6)',
                        'rgba(255, 206, 86, 0.6)',
                        'rgba(75, 192, 192, 0.6)',
                        'rgba(153, 102, 255, 0.6)',
                        'rgba(255, 159, 64, 0.6)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                legend: {
                    position: 'right'
                }
            }
        });

        // Show low stock modal if there are low stock items
        $(document).ready(function() {
            <?php if ($result_low_stock_items->num_rows > 0): ?>
                $('#lowStockModal').modal('show');
            <?php endif; ?>
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
