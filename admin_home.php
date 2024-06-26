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


$sql = "SELECT branch, item_type, quantity FROM inventory";
$result = $conn->query($sql);


$branches = [];
$itemTypes = [];
$data = [];


if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $branch = $row['branch'];
        $item = $row['item_type'];
        $quantity = (int)$row['quantity'];

       
        if (!in_array($branch, $branches)) {
            $branches[] = $branch;
        }
        if (!in_array($item, $itemTypes)) {
            $itemTypes[] = $item;
        }

       
        $data[$branch][$item] = $quantity;
    }
}


$conn->close();


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
<html>
<head>
    <!-- <title>Inventory Statistics - Column Chart</title> -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-4">
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

    <!-- Bootstrap JS for optional responsive features -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
