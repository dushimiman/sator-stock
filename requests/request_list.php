<?php
session_start();

include(__DIR__ . '/../includes/db.php'); 
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    header("Location: ../login.php"); 
    exit();
}
function checkItemAvailability($mysqli, $item_name, $requested_quantity) {
    $stmt = $mysqli->prepare("SELECT SUM(quantity) AS total_quantity FROM stock WHERE item_name = ?");
    if (!$stmt) {
        die("Error preparing stock query: " . $mysqli->error);
    }
    $stmt->bind_param("s", $item_name);
    $stmt->execute();
    $result = $stmt->get_result();

    $total_quantity = 0;
    if ($result->num_rows == 1) {
        $stock = $result->fetch_assoc();
        $total_quantity += $stock['total_quantity'];
    }

    return $total_quantity >= $requested_quantity;
}

// Approve request
function approveRequest($mysqli, $request_id) {
    $stmt = $mysqli->prepare("SELECT * FROM requests WHERE id = ?");
    $stmt->bind_param("i", $request_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $request = $result->fetch_assoc();
        $item_name = $request['item_name'];
        $requested_quantity = $request['quantity'];

        if (checkItemAvailability($mysqli, $item_name, $requested_quantity)) {
            $stmt = $mysqli->prepare("UPDATE requests SET status = 'approved' WHERE id = ?");
            $stmt->bind_param("i", $request_id);

            if ($stmt->execute()) {
                echo "Request approved successfully for item '$item_name'.";
                return true;
            } else {
                echo "Error updating request status: " . $mysqli->error;
            }
        } else {
            $stmt = $mysqli->prepare("SELECT SUM(quantity) AS total_quantity FROM stock WHERE item_name = ?");
            $stmt->bind_param("s", $item_name);
            $stmt->execute();
            $result = $stmt->get_result();
            $stock = $result->fetch_assoc();
            $available_quantity = $stock['total_quantity'];

            echo "Error: Requested quantity ($requested_quantity) exceeds available stock for item '$item_name'. In stock we have $available_quantity quantity.";
        }
    } else {
        echo "Error: Request not found or multiple requests found.";
    }

    return false;
}

// Handle approve action
if (isset($_GET['action']) && $_GET['action'] === 'Approve' && isset($_GET['id'])) {
    $request_id = intval($_GET['id']);
    if (approveRequest($mysqli, $request_id)) {
        header("Location: request_list.php");
        exit();
    } else {
        echo "Error approving request.";
    }
}

// Search requests
$searchTerm = '';
if (isset($_GET['search'])) {
    $searchTerm = $mysqli->real_escape_string($_GET['search']);
    $sql = "SELECT * FROM requests WHERE requested_by LIKE '%$searchTerm%' OR item_name LIKE '%$searchTerm%'";
} else {
    $sql = "SELECT * FROM requests";
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
                            $id = intval($row['id']);
                            $action_url = "?action=Approve&id=" . $id;
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['requisition_date']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['requested_by']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['item_name']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['quantity']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['status']) . "</td>";
                            echo "<td>";
                            if ($row['status'] == 'pending') {
                                echo "<a class='btn btn-success btn-sm' href='$action_url'>Approve</a> ";
                            }
                            echo "<a class='btn btn-primary btn-sm' href='view_request.php?id=" . $id . "'>View Details</a>";
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
