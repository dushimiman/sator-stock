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

// Query inventory table
$sql = "SELECT branch, item_type, quantity FROM inventory";
$result = $conn->query($sql);


$branches = [];
$itemTypes = [];
$data = [];

// Process query results
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $branch = $row['branch'];
        $item = $row['item_type'];
        $quantity = (int)$row['quantity'];

        // Collect branches and item types
        if (!in_array($branch, $branches)) {
            $branches[] = $branch;
        }
        if (!in_array($item, $itemTypes)) {
            $itemTypes[] = $item;
        }

        // Build data structure for chart
        $data[$branch][$item] = $quantity;
    }
}

$conn->close();

// Prepare datasets for Chart.js
$datasets = [];
foreach ($itemTypes as $item) {
    $dataset = [
        'label' => $item,
        'data' => [],
        'backgroundColor' => '#' . substr(md5(rand()), 0, 6),
    ];

    foreach ($branches as $branch) {
        $dataset['data'][] = isset($data[$branch][$item]) ? $data[$branch][$item] : 0;
    }

    $datasets[] = $dataset;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="container mt-4">
        <h2 class="text-center">Admin Dashboard</h2>
        <div class="row">
            <div class="col-md-6">
                <canvas id="myChart" height="300"></canvas>
            </div>
        </div>
    </div>

    <script>
        var ctx = document.getElementById('myChart').getContext('2d');
        var myChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?= json_encode($branches) ?>,
                datasets: <?= json_encode($datasets) ?>
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(tooltipItem) {
                                return tooltipItem.dataset.label + ': ' + tooltipItem.raw.toFixed(2);
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>

    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
