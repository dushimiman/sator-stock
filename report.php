<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "stock_management_system";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


function getTotalItemCount() {
    global $conn;
    $query = "SELECT COUNT(*) AS total_items FROM items";
    $result = $conn->query($query);
    $row = $result->fetch_assoc();
    return $row['total_items'];
}


function getTotalReturnedItemCount() {
    global $conn;
    $query = "SELECT COUNT(*) AS total_returned_items FROM returned_items";
    $result = $conn->query($query);
    $row = $result->fetch_assoc();
    return $row['total_returned_items'];
}


function getAddedItemCount($startDate, $endDate) {
    global $conn;
    $query = "SELECT COUNT(*) AS added_items FROM items WHERE creation_date >= ? AND creation_date <= ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $startDate, $endDate);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row['added_items'];
}



function getEditedItemCount($startDate, $endDate) {
    global $conn;
    $query = "SELECT COUNT(*) AS edited_items FROM items WHERE last_updated >= ? AND last_updated <= ?";
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        die("Error preparing statement: " . $conn->error);
    }
    $stmt->bind_param("ss", $startDate, $endDate);
    $stmt->execute();
    $result = $stmt->get_result();
    if (!$result) {
        die("Error executing query: " . $stmt->error);
    }
    $row = $result->fetch_assoc();
    return $row['edited_items'];
}


function getRequestedItemCount($startDate, $endDate) {
    global $conn;
    $query = "SELECT COUNT(*) AS requested_items FROM requisitions WHERE requisition_date >= ? AND requisition_date <= ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $startDate, $endDate);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row['requested_items'];
}

// Function to get count of returned items for a specific period
function getReturnedItemCount($startDate, $endDate) {
    global $conn;
    $query = "SELECT COUNT(*) AS returned_items FROM returned_items WHERE returned_date >= ? AND returned_date <= ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $startDate, $endDate);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row['returned_items'];
}

// Fetch data based on report type
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $reportType = $_POST['report_type'];
    $startDate = $_POST['start_date'];
    $endDate = $_POST['end_date'];

    switch ($reportType) {
        case 'daily':
            $startDate = date('Y-m-d');
            $endDate = date('Y-m-d');
            break;
        case 'monthly':
            $startDate = date('Y-m-01');
            $endDate = date('Y-m-t');
            break;
        case 'custom':
            // Assuming date format is YYYY-MM-DD
            break;
        default:
            break;
    }

    // Calculate counts
    $addedItemsCount = getAddedItemCount($startDate, $endDate);
    $editedItemsCount = getEditedItemCount($startDate, $endDate);
    $requestedItemsCount = getRequestedItemCount($startDate, $endDate);
    $returnedItemsCount = getReturnedItemCount($startDate, $endDate);
    $totalItemsCount = getTotalItemCount();
    $totalReturnedItemsCount = getTotalReturnedItemCount();

    // Display report
    echo "<h2>Report Summary</h2>";
    echo "<p><strong>Report Type:</strong> " . ucfirst($reportType) . "</p>";
    echo "<p><strong>Period:</strong> " . $startDate . " to " . $endDate . "</p>";
    echo "<table class='table table-bordered'>";
    echo "<thead>";
    echo "<tr><th>Operation</th><th>Count</th></tr>";
    echo "</thead>";
    echo "<tbody>";
    echo "<tr><td>Items Added</td><td>" . $addedItemsCount . "</td></tr>";
    echo "<tr><td>Items Edited</td><td>" . $editedItemsCount . "</td></tr>";
    echo "<tr><td>Items Requested</td><td>" . $requestedItemsCount . "</td></tr>";
    echo "<tr><td>Items Returned</td><td>" . $returnedItemsCount . "</td></tr>";
    echo "</tbody>";
    echo "</table>";
    echo "<p><strong>Total Items in Stock:</strong> " . $totalItemsCount . "</p>";
    echo "<p><strong>Total Returned Items:</strong> " . $totalReturnedItemsCount . "</p>";
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generate Report</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h2 class="text-center">Generate Report</h2>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                            <div class="form-group">
                                <label for="report_type">Report Type:</label>
                                <select id="report_type" name="report_type" class="form-control">
                                    <option value="daily">Daily</option>
                                    <option value="monthly">Monthly</option>
                                    <option value="custom">Custom (Date Range)</option>
                                </select>
                            </div>
                            <div class="form-group" id="date_range">
                                <label for="start_date">Start Date:</label>
                                <input type="date" id="start_date" name="start_date" class="form-control">
                            </div>
                            <div class="form-group" id="date_range">
                                <label for="end_date">End Date:</label>
                                <input type="date" id="end_date" name="end_date" class="form-control">
                            </div>
                            <button type="submit" class="btn btn-primary">Generate Report</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
