<?php
include('includes/nav_bar.php');
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "stock_management_system";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

function getTotalQuantity($conn, $table, $dateFrom, $dateTo) {
    $sql = "SELECT item_name, SUM(quantity) AS total_quantity FROM $table WHERE requisition_date BETWEEN ? AND ? GROUP BY item_name";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $dateFrom, $dateTo);
    
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            echo "<div class='table-responsive'>";
            echo "<table class='table table-striped table-bordered'>";
            echo "<thead class='thead-dark'>";
            echo "<tr><th>Item Name</th><th>Total Quantity</th></tr>";
            echo "</thead>";
            echo "<tbody>";
            
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row["item_name"] . "</td>";
                echo "<td>" . $row["total_quantity"] . "</td>";
                echo "</tr>";
            }
            
            echo "</tbody></table>";
            echo "</div>";
        } else {
            echo "<p class='text-center'>No items found within the specified dates.</p>";
        }
    } else {
        echo "Error executing query: " . $stmt->error;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $dateFrom = $_POST['date_from'];
    $dateTo = $_POST['date_to'];
    
    getTotalQuantity($conn, 'requests', $dateFrom, $dateTo);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report Result</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="card">
                    <div class="card-header">
                        <h2 class="text-center">Report Result</h2>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                            <div class="form-group">
                                <label for="date_from">From:</label>
                                <input type="date" id="date_from" name="date_from" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="date_to">To:</label>
                                <input type="date" id="date_to" name="date_to" class="form-control" required>
                            </div>
                            <button type="submit" class="btn btn-primary btn-block">Generate Report</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

<?php
$conn->close();
?>
