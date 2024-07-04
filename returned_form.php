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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $serialNumber = $_POST['serial_number'];
    $returnedBy = $_POST['returned_by'];
    $receivedBy = $_SESSION['username']; 
    $returnReason = $_POST['return_reason'];

    $checkQuery = "SELECT * FROM requisitions WHERE serial_number = ?";
    $stmtCheck = $conn->prepare($checkQuery);
    $stmtCheck->bind_param("s", $serialNumber);
    $stmtCheck->execute();
    $result = $stmtCheck->get_result();

    if ($result->num_rows > 0) {
        
        $insertQuery = "INSERT INTO returned_items (serial_number, returned_by, received_by, return_reason) VALUES (?, ?, ?, ?)";
        $stmtInsert = $conn->prepare($insertQuery);
        $stmtInsert->bind_param("ssss", $serialNumber, $returnedBy, $receivedBy, $returnReason);

        if ($stmtInsert->execute()) {
            echo "Item returned successfully.";
        } else {
            echo "Error returning item: " . $stmtInsert->error;
        }
    } else {
        echo "Serial number not found in requisitions table.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Items Return Form</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .card-body {
            padding: 1.25rem; /* Adjust padding as needed */
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h2 class="text-center">Items Return Form</h2>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                            <div class="form-group">
                                <label for="serial_number">Serial Number:</label>
                                <input type="text" id="serial_number" name="serial_number" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="returned_by">Returned By:</label>
                                <input type="text" id="returned_by" name="returned_by" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="received_by">Received By:</label>
                                <input type="text" id="received_by" name="received_by" class="form-control" value="<?php echo $_SESSION['username']; ?>" readonly>
                            </div>
                            <div class="form-group">
                                <label for="return_reason">Return Reason:</label>
                                <textarea id="return_reason" name="return_reason" class="form-control" rows="3" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary btn-block">Submit Return</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

<?php
$conn->close();
?>
