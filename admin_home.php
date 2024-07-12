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

try {
    $pdo = new PDO('mysql:host=localhost;dbname=stock_management_system', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    
    $stmt = $pdo->prepare("SELECT item_type, SUM(quantity) AS total_quantity FROM stock GROUP BY item_type HAVING SUM(quantity) < 5");
    $stmt->execute();
    $low_stock_items = $stmt->fetchAll(PDO::FETCH_ASSOC); 
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
}

$sql = "SELECT item_type, COUNT(*) as count FROM stock GROUP BY item_type";
$result = $conn->query($sql);

$dataPoints = array();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $dataPoint = array("label" => $row['item_type'], "y" => $row['count']);
        array_push($dataPoints, $dataPoint);
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Items in Stock</title>
    
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    
    <style>
        body {
            padding: 20px;
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
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-8 offset-lg-2">
                <h1 class="mt-4 mb-4 text-center">Items in Stock</h1>
                <div id="itemChartContainer">
                    <canvas id="itemChart"></canvas>
                </div>
            </div>
        </div>

       
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
                        <?php if (!empty($low_stock_items)): ?>
                            <p>Some items have low quantities in stock:</p>
                            <ul>
                                <?php foreach ($low_stock_items as $item): ?>
                                    <li><?php echo htmlspecialchars($item['item_type']) . ' (Total Quantity: ' . htmlspecialchars($item['total_quantity']) . ')'; ?></li>
                                <?php endforeach; ?>
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

    <script>
        var dataPoints = <?php echo json_encode($dataPoints, JSON_NUMERIC_CHECK); ?>;
        
        var ctx = document.getElementById('itemChart').getContext('2d');
        var itemChart = new Chart(ctx, {
            type: 'doughnut', // Use doughnut chart for circular statistics
            data: {
                labels: dataPoints.map(dp => dp.label),
                datasets: [{
                    label: 'Item Counts',
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
                maintainAspectRatio: false, // Ensure chart maintains aspect ratio
                legend: {
                    position: 'right' // Position legend on the right side
                }
            }
        });

        // Show the low stock modal on page load
        $('#lowStockModal').modal('show');
    </script>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
