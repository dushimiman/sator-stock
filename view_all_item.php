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
    $sql = "SELECT * FROM stock";
    
    if (!empty($search)) {
        $sql .= " WHERE item_name LIKE '%$search%' OR item_type LIKE '%$search%' OR serial_number LIKE '%$search%' OR creation_date LIKE '%$search%'";
    }
    
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo "<div class='table-responsive'>";
        echo "<table class='table table-striped table-bordered'>";
        echo "<thead class='thead-dark'>";
        echo "<tr><th>ID</th><th>Name</th><th>Type</th><th>Serial Number</th><th>Quantity</th><th>Creation Date</th><th>Status</th><th>Actions</th></tr>";
        echo "</thead>";
        echo "<tbody>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row["id"] . "</td>";
            echo "<td>" . $row["item_name"] . "</td>";
            echo "<td>" . $row["item_type"] . "</td>";
            echo "<td>" . $row["serial_number"] . "</td>";
            echo "<td>" . $row["quantity"] . "</td>";
            echo "<td>" . $row["creation_date"] . "</td>";
            echo "<td>" . $row["status"] . "</td>";
            echo "<td><button type='button' class='btn btn-primary btn-sm' data-toggle='modal' data-target='#editModal" . $row["id"] . "'>Edit</button></td>";
            echo "</tr>";
            
            
            echo "<div class='modal fade' id='editModal" . $row["id"] . "' tabindex='-1' role='dialog' aria-labelledby='editModalLabel" . $row["id"] . "' aria-hidden='true'>";
            echo "<div class='modal-dialog' role='document'>";
            echo "<div class='modal-content'>";
            echo "<div class='modal-header'>";
            echo "<h5 class='modal-title' id='editModalLabel" . $row["id"] . "'>Edit Item</h5>";
            echo "<button type='button' class='close' data-dismiss='modal' aria-label='Close'>";
            echo "<span aria-hidden='true'>&times;</span>";
            echo "</button>";
            echo "</div>";
            echo "<form action='editItem.php' method='POST'>";
            echo "<div class='modal-body'>";
            echo "<input type='hidden' name='item_id' value='" . $row["id"] . "'>";
            echo "<div class='form-group'>";
            echo "<label for='edit_name'>Name:</label>";
            echo "<input type='text' id='edit_name' name='edit_name' class='form-control' value='" . $row["item_name"] . "' required>";
            echo "</div>";
            echo "<div class='form-group'>";
            echo "<label for='edit_type'>Type:</label>";
            echo "<input type='text' id='edit_type' name='edit_type' class='form-control' value='" . $row["item_type"] . "' required>";
            echo "</div>";
            echo "<div class='form-group'>";
            echo "<label for='edit_serial_number'>Serial Number:</label>";
            echo "<input type='text' id='edit_serial_number' name='edit_serial_number' class='form-control' value='" . $row["serial_number"] . "' >";
            echo "</div>";
            echo "<div class='form-group'>";
            echo "<label for='edit_quantity'>Quantity:</label>";
            echo "<input type='text' id='edit_quantity' name='edit_quantity' class='form-control' value='" . $row["quantity"] . "' >";
            echo "</div>";
            echo "<div class='form-group'>";
            echo "<label for='edit_status'>Status:</label>";
            echo "<input type='text' id='edit_status' name='edit_status' class='form-control' value='" . $row["status"] . "' >";
            echo "</div>";
            echo "</div>";
            echo "<div class='modal-footer'>";
            echo "<button type='button' class='btn btn-secondary' data-dismiss='modal'>Close</button>";
            echo "<button type='submit' class='btn btn-primary'>Save changes</button>";
            echo "</div>";
            echo "</form>";
            echo "</div>";
            echo "</div>";
            echo "</div>";
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
                        <h2 class="text-center">View All Items</h2>
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
