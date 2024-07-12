<?php
include('includes/nav_bar.php');
include('includes/db.php');

$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';

function approveRequest($conn, $request_id) {
    $sql = "UPDATE requests SET status = 'approved' WHERE id = $request_id";
    if ($conn->query($sql) === true) {
        return true;
    } else {
        return false;
    }
}

$sql = "SELECT * FROM requests";


if (!empty($searchTerm)) {
    $searchTerm = $conn->real_escape_string($searchTerm);
    $sql .= " WHERE requested_by LIKE '%$searchTerm%' OR item_name LIKE '%$searchTerm%' ";
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

                        if ($row['status'] === 'pending') {
                            echo "<span class='text-muted'>Pending Approval</span>";
                        } elseif ($row['status'] === 'approved') {
                            echo "<a class='btn btn-warning btn-sm' href='out_item_form.php?id=" . $row['id'] . "'>Out Item</a>";
                        }

                        echo "<a class='btn btn-primary btn-sm ml-1' href='view_request.php?id=" . $row['id'] . "'>View Details</a>";
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
