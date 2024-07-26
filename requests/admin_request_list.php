<?php
session_start();
include(__DIR__ . '/../includes/nav_bar.php');
include(__DIR__ . '/../includes/db.php'); 
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    header("Location: ../login.php"); 
    exit();
}
$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';

function approveRequest($mysqli, $request_id) {
    $sql = "UPDATE requests SET status = 'approved' WHERE id = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("i", $request_id);

    if ($stmt->execute()) {
        return true;
    } else {
        return false;
    }
}

$sql = "SELECT * FROM requests";

if (!empty($searchTerm)) {
    $searchTerm = $mysqli->real_escape_string($searchTerm);
    $sql .= " WHERE requested_by LIKE '%$searchTerm%' OR item_name LIKE '%$searchTerm%'";
}

$result = $mysqli->query($sql);

if ($result === false) {
    die("Error executing the query: " . $mysqli->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>All Requests</title>
    <link rel="icon" href="./images/stock-icon.png" type="image/x-icon"> 
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .btn-out-success {
            background-color: #28a745 !important; 
            border-color: #28a745 !important;
        }
        .btn-out-stock {
            background-color: #dc3545 !important; 
            border-color: #dc3545 !important;
        }
    </style>
</head>
<body>

<div class="container mt-4">
    <h2 class="text-center mb-4">All Requests</h2>
    <form class="form-inline mb-4" method="GET" action="">
        <input class="form-control mr-sm-2" type="search" name="search" placeholder="Search" aria-label="Search" value="<?php echo htmlspecialchars($searchTerm); ?>">
        <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Search</button>
    </form>
    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead class="thead-light">
                <tr>
                    <th>ID</th>
                    <th>Requisition Date</th>
                    <th>Requested By</th>
                    <th>Item Name</th>
                    <th>Quantity</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row['id'] . "</td>";
                        echo "<td>" . $row['requisition_date'] . "</td>";
                        echo "<td>" . $row['requested_by'] . "</td>";
                        echo "<td>" . $row['item_name'] . "</td>";
                        echo "<td>" . $row['quantity'] . "</td>";
                        echo "<td>" . $row['status'] . "</td>";
                        echo "<td>";
                        echo "<a class='btn btn-warning btn-sm btn-out-success' href='out_item_form.php?id=" . $row['id'] . "'>Out Item</a>";
                        // echo "<a class='btn btn-danger btn-sm btn-out-stock ml-1' href='out_stock.php?id=" . $row['id'] . "'>Out of Stock</a>";
                        echo "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='7' class='text-center'>No requests found</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

<?php
$mysqli->close();
?>
