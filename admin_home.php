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

$sql = "SELECT item_name, COUNT(*) as count FROM stock GROUP BY item_name";
$result = $conn->query($sql);

$dataPoints = array();

if ($result->num_rows > 0) {
    
    while ($row = $result->fetch_assoc()) {
        $dataPoint = array("label" => $row['item_name'], "y" => $row['count']);
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
    
    <style>
        
        body {
            padding: 20px;
        }
        #itemChartContainer {
            width: 60%;
            margin: 0 auto;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="mt-4 mb-4">Items in Stock</h1>
        <div id="itemChartContainer">
            <canvas id="itemChart"></canvas>
        </div>
    </div>

    <script>
       
        var dataPoints = <?php echo json_encode($dataPoints, JSON_NUMERIC_CHECK); ?>;
        
        
        var ctx = document.getElementById('itemChart').getContext('2d');
        var itemChart = new Chart(ctx, {
            type: 'bar', 
            data: {
                labels: dataPoints.map(dp => dp.label),
                datasets: [{
                    label: 'Item Counts',
                    data: dataPoints.map(dp => dp.y),
                    backgroundColor: 'rgba(54, 162, 235, 0.6)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true
                        },
                        scaleLabel: {
                            display: true,
                            labelString: 'Quantity'
                        }
                    }],
                    xAxes: [{
                        scaleLabel: {
                            display: true,
                            labelString: 'Item Types'
                        }
                    }]
                }
            }
        });
    </script>

    
    
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>