<?php
session_start();
include(__DIR__ . '/../includes/nav_bar.php');
include(__DIR__ . '/../includes/db.php'); 
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    header("Location: ../login.php"); 
    exit();
}
function viewReturnedItems($conn, $search = '') {
    $sql = "SELECT * FROM returned_items";
    
    if (!empty($search)) {
        $sql .= " WHERE serial_number LIKE '%$search%' OR returned_by LIKE '%$search%' OR received_by LIKE '%$search%' OR return_reason LIKE '%$search%' OR returned_date LIKE '%$search%'";
    }
    
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo "<div class='table-responsive'>";
        echo "<table class='table table-striped table-bordered'>";
        echo "<thead class='thead-dark'>";
        echo "<tr><th>ID</th><th>Item Name</th><th>Serial Number</th><th>Returned By</th><th>Received By</th><th>Return Reason</th><th>Return Date</th></tr>";
        echo "</thead>";
        echo "<tbody>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row["id"] . "</td>";
            echo "<td>" . $row["item_name"] . "</td>";
            echo "<td>" . $row["serial_number"] . "</td>";
            echo "<td>" . $row["returned_by"] . "</td>";
            echo "<td>" . $row["received_by"] . "</td>";
            echo "<td>" . $row["return_reason"] . "</td>";
            echo "<td>" . $row["returned_date"] . "</td>";
            echo "</tr>";
        }
        echo "</tbody></table>";
        echo "</div>";
    } else {
        echo "<p class='text-center'>No returned items found.</p>";
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
    <title>View Returned Items</title>
    <link rel="icon" href="./images/stock-icon.png" type="image/x-icon"> 
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
                        <h2 class="text-center">View Returned Items</h2>
                    </div>
                    <div class="card-body">
                        <?php
                            viewReturnedItems($conn, $search);
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
