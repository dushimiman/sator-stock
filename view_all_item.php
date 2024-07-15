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

function getAllItems($conn, $search = '') {
    $sql = "
        SELECT id, item_name, item_type, serial_number, imei, quantity, creation_date
        FROM stock
        WHERE status = 'new item'
        UNION
        SELECT id, item_name, item_type, serial_number, imei, quantity, creation_date
        FROM returned_items
        WHERE is_working = 1";
    
    if (!empty($search)) {
        $sql .= " AND (item_name LIKE '%$search%' OR item_type LIKE '%$search%' OR serial_number LIKE '%$search%' OR creation_date LIKE '%$search%')";
    }
    
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo "<div class='table-responsive'>";
        echo "<table class='table table-striped table-bordered'>";
        echo "<thead class='thead-dark'>";
        echo "<tr><th>ID</th><th>Name</th><th>Type</th><th>Serial Number</th><th>IMEI</th><th>Quantity</th><th>Creation Date</th></tr>";
        echo "</thead>";
        echo "<tbody>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row["id"] . "</td>";
            echo "<td>" . $row["item_name"] . "</td>";
            echo "<td>" . $row["item_type"] . "</td>";
            echo "<td>" . $row["serial_number"] . "</td>";
            echo "<td>" . $row["imei"] . "</td>";
            echo "<td>" . $row["quantity"] . "</td>";
            echo "<td>" . $row["creation_date"] . "</td>";
            echo "</tr>";
        }
        echo "</tbody></table>";
        echo "</div>";
    } else {
        echo "<p class='text-center'>No items found.</p>";
    }
}

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['search'])) {
    $search = $_GET['search'];
} else {
    $search = '';
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View All Items</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-12">
                <div class="search-container">
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="GET" class="form-inline">
                        <div class="form-group mr-2">
                            <label for="search" class="sr-only">Search:</label>
                            <input type="text" id="search" name="search" class="form-control" placeholder="Search..." value="<?php echo htmlspecialchars($search); ?>">
                        </div>
                        <button type="submit" class="btn btn-primary">Search</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="text-center">View All New Items</h4>
                    </div>
                    <div class="card-body">
                        <?php
                            getAllItems($conn, $search);
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
   
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

<?php
$conn->close();
?>
