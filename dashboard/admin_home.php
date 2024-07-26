<?php
include(__DIR__ . '/../includes/nav_bar.php');
include(__DIR__ . '/../includes/db.php');

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

$sql = "SELECT item_type, SUM(quantity) AS total_count FROM stock GROUP BY item_type";
$result = $mysqli->query($sql);

$dataPoints = array();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $dataPoint = array("label" => $row['item_type'], "y" => $row['total_count']);
        array_push($dataPoints, $dataPoint);
    }
}

$sql_low_stock_items = "SELECT item_type, SUM(quantity) AS total_count FROM stock GROUP BY item_type HAVING SUM(quantity) < 5";
$result_low_stock_items = $mysqli->query($sql_low_stock_items);

$sql_total_items_in_stock = "SELECT COUNT(DISTINCT item_type) AS total_items_in_stock FROM stock";
$result_total_items_in_stock = $mysqli->query($sql_total_items_in_stock);
$total_items_in_stock = $result_total_items_in_stock->num_rows > 0 ? $result_total_items_in_stock->fetch_assoc()['total_items_in_stock'] : 0;

$sql_total_items_requested = "SELECT COUNT(*) AS total_items_requested FROM requests";
$result_total_items_requested = $mysqli->query($sql_total_items_requested);
$total_items_requested = $result_total_items_requested->num_rows > 0 ? $result_total_items_requested->fetch_assoc()['total_items_requested'] : 0;

$sql_total_items_out_of_stock = "SELECT COUNT(*) AS total_items_out_of_stock FROM out_in_stock";
$result_total_items_out_of_stock = $mysqli->query($sql_total_items_out_of_stock);
$total_items_out_of_stock = $result_total_items_out_of_stock->num_rows > 0 ? $result_total_items_out_of_stock->fetch_assoc()['total_items_out_of_stock'] : 0;

$sql_total_repaired_items = "SELECT COUNT(*) AS total_repaired_items FROM returned_items WHERE is_working = 1";
$result_total_repaired_items = $mysqli->query($sql_total_repaired_items);
$total_repaired_items = $result_total_repaired_items->num_rows > 0 ? $result_total_repaired_items->fetch_assoc()['total_repaired_items'] : 0;

$mysqli->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock Management Dashboard</title>
    <link rel="icon" href="../images/stock-icon.png" type="image/x-icon"> 
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
                    <a class="dropdown-item" href="../logout.php">Logout</a>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card text-white bg-primary">
                    <a href="../stock/view_all_item.php" class="text-white">
                        <div class="card-body">
                            <h5 class="card-title">Total items in Stock</h5>
                            <p class="card-text"><?php echo $total_items_in_stock; ?></p>
                        </div>
                    </a>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card text-white bg-secondary">
                    <a href="../requests/admin_request_list.php" class="text-white">
                        <div class="card-body">
                            <h5 class="card-title">Total Items Requested</h5>
                            <p class="card-text"><?php echo $total_items_requested; ?></p>
                        </div>
                    </a>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card text-white bg-danger">
                    <a href="../stock/view_out_in_stock.php" class="text-white">
                        <div class="card-body">
                            <h5 class="card-title">Total Items Out of Stock</h5>
                            <p class="card-text"><?php echo $total_items_out_of_stock; ?></p>
                        </div>
                    </a>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card text-white bg-success">
                    <a href="../stock/view_repair_item.php" class="text-white">
                        <div class="card-body">
                            <h5 class="card-title">Total Repaired Items</h5>
                            <p class="card-text"><?php echo $total_repaired_items; ?></p>
                        </div>
                    </a>
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
                                                    <li><?php echo htmlspecialchars($row['item_type']) . ' (Total Quantity: ' . htmlspecialchars($row['total_count']) . ')'; ?></li>
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
    
    <script>
window.onload = function() {
    var ctx = document.getElementById('itemChart').getContext('2d');
    var itemChart = new Chart(ctx, {
        type: 'pie',
        data: {
            labels: <?php echo json_encode(array_column($dataPoints, "label")); ?>,
            datasets: [{
                label: 'Item Types in Stock',
                data: <?php echo json_encode(array_column($dataPoints, "y")); ?>,
                backgroundColor: [
                    'rgba(255, 99, 132, 0.2)',
                    'rgba(54, 162, 235, 0.2)',
                    'rgba(255, 206, 86, 0.2)',
                    'rgba(75, 192, 192, 0.2)',
                    'rgba(153, 102, 255, 0.2)',
                    'rgba(255, 159, 64, 0.2)'
                ],
                borderColor: [
                    'rgba(255, 99, 132, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)',
                    'rgba(255, 159, 64, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            title: {
                display: true,
                text: 'Items in Stock by Type'
            }
        }
    });

    <?php if ($result_low_stock_items->num_rows > 0): ?>
    $('#lowStockModal').modal('show');
    <?php endif; ?>
};
    </script>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://kit.fontawesome.com/a076d05399.js"></script>
</body>
</html>
