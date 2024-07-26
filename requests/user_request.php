<?php
session_start();
include(__DIR__ . '/../includes/user_nav_bar.php');
include(__DIR__ . '/../includes/db.php'); 
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    header("Location: ../login.php"); 
    exit();
}

if (!isset($_SESSION['username'])) {
    echo "Session error: User not logged in.";
    exit();
}

$username = $_SESSION['username'];

$search_query = isset($_POST['search_query']) ? $_POST['search_query'] : '';

$sql = "SELECT * FROM requests WHERE username = ?";

if (!empty($search_query)) {
    $sql .= " AND (requested_by LIKE ? OR item_name LIKE ? OR requisition_date LIKE ? OR location LIKE ? OR reasons LIKE ?)";
}

$stmt = $mysqli->prepare($sql);

if ($stmt === false) {
    die("Error preparing the query: " . $mysqli->error);
}

$params = [];
$types = 's'; 
$params[] = $username;

if (!empty($search_query)) {
    $search_like = '%' . $search_query . '%';
    $params = array_merge($params, array_fill(0, 5, $search_like));
    $types .= str_repeat('s', 5); 
}

$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Requests</title>
    <link rel="icon" href="./images/stock-icon.png" type="image/x-icon"> 
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">Your Requests</h1>

        <form method="post" class="mb-4">
            <div class="form-group">
                <label for="search_query">Search:</label>
                <input type="text" id="search_query" name="search_query" class="form-control" value="<?php echo htmlspecialchars($search_query); ?>">
            </div>
            <button type="submit" class="btn btn-primary">Search</button>
        </form>

        <?php
        if ($result->num_rows > 0) {
            echo '<div class="table-responsive">';
            echo '<table class="table table-bordered table-striped">';
            echo '<thead class="thead-dark">';
            echo '<tr><th>Date</th><th>Requested By</th><th>Item Name</th><th>Quantity</th><th>Status</th><th>Reasons</th></tr>';
            echo '</thead>';
            echo '<tbody>';
            while ($row = $result->fetch_assoc()) {
                echo '<tr>';
                echo '<td>' . htmlspecialchars($row['requisition_date']) . '</td>';
                echo '<td>' . htmlspecialchars($row['requested_by']) . '</td>';
                echo '<td>' . htmlspecialchars($row['item_name']) . '</td>';
                echo '<td>' . htmlspecialchars($row['quantity']) . '</td>';
                echo '<td>' . htmlspecialchars($row['status']) . '</td>';
                echo '<td>' . htmlspecialchars($row['reasons']) . '</td>';
                echo '</tr>';
            }
            echo '</tbody>';
            echo '</table>';
            echo '</div>';
        } else {
            echo '<div class="alert alert-warning">No requests found.</div>';
        }
        ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

<?php
$stmt->close();
$mysqli->close();
?>
