<?php
include('includes/db.php');

function checkItemAvailability($conn, $item_name, $requested_quantity) {
    // Check in stock table
    $stmt = $conn->prepare("SELECT SUM(quantity) AS total_quantity FROM stock WHERE item_name = ?");
    if (!$stmt) {
        die("Error preparing stock query: " . $conn->error);
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

function approveRequest($conn, $request_id) {
    $stmt = $conn->prepare("SELECT * FROM requests WHERE id = ?");
    $stmt->bind_param("i", $request_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $request = $result->fetch_assoc();
        $item_name = $request['item_name'];
        $requested_quantity = $request['quantity'];

        if (checkItemAvailability($conn, $item_name, $requested_quantity)) {
            // Update request status to 'approved'
            $stmt = $conn->prepare("UPDATE requests SET status = 'approved' WHERE id = ?");
            $stmt->bind_param("i", $request_id);

            if ($stmt->execute()) {
                echo "Request approved successfully for item '$item_name'.";
                return true;
            } else {
                echo "Error updating request status: " . $conn->error;
            }
        } else {
            echo "Error: Requested quantity ($requested_quantity) exceeds available stock for item '$item_name'.";
        }
    } else {
        echo "Error: Request not found or multiple requests found.";
    }

    return false;
}

// Approve request action
if (isset($_GET['action']) && $_GET['action'] === 'Approve' && isset($_GET['id'])) {
    $request_id = $_GET['id'];
    if (approveRequest($conn, $request_id)) {
        header("Location: request_list.php");
        exit();
    } else {
        echo "Error approving request.";
    }
}

// Fetch requests list
$searchTerm = '';
if (isset($_GET['search'])) {
    $searchTerm = $conn->real_escape_string($_GET['search']);
    $sql = "SELECT * FROM requests WHERE requested_by LIKE '%$searchTerm%' OR item_name LIKE '%$searchTerm%'";
} else {
    $sql = "SELECT * FROM requests";
}

$result = $conn->query($sql);

if ($result === false) {
    die("Error executing the query: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>All Requests</title>
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
                            echo "<tr>";
                            echo "<td>" . $row['id'] . "</td>";
                            echo "<td>" . $row['requisition_date'] . "</td>";
                            echo "<td>" . $row['requested_by'] . "</td>";
                            echo "<td>" . $row['item_name'] . "</td>";
                            echo "<td>" . $row['quantity'] . "</td>";
                            echo "<td>" . $row['status'] . "</td>";
                            echo "<td>";
                            if ($row['status'] == 'pending') {
                                echo "<a class='btn btn-success btn-sm' href='?action=Approve&id=" . $row['id'] . "'>Approve</a> ";
                            }
                            echo "<a class='btn btn-primary btn-sm' href='view_request.php?id=" . $row['id'] . "'>View Details</a>";
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
$conn->close();
?>
