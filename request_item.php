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

// Function to generate requisition number
function generateRequisitionNumber() {
    $date = date("Ymd");
    global $conn;
    $query = "SELECT COUNT(*) as count FROM requisitions WHERE requisition_number LIKE '$date%'";
    $result = $conn->query($query);
    $row = $result->fetch_assoc();
    $number = $row['count'] + 1;

    return $date . "-" . sprintf("%02d", $number);
}

// Function to fetch item details based on serial number (for AJAX request)
if (isset($_GET['serial_number'])) {
    $serialNumber = $_GET['serial_number'];
    $query = "SELECT name, type FROM items WHERE serial_number = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $serialNumber);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo json_encode($row);
    } else {
        echo json_encode(['error' => 'Item not found']);
    }
    exit;
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $requisitionDate = $_POST['requisition_date'];
    $requisitionNumber = generateRequisitionNumber();
    $requestedBy = $_POST['requested_by'];
    $requesterPosition = $_POST['requester_position'];
    $approvedBy = $_SESSION['username']; // Fetch from logged-in session
    $approverPosition = $_SESSION['role']; // Fetch from logged-in session
    $serialNumber = $_POST['serial_number'];
    $itemName = $_POST['item_name'] ?? ''; // Use null coalescing operator to handle undefined array key
    $itemType = $_POST['item_type'] ?? ''; // Use null coalescing operator to handle undefined array key
    $paymentMethod = $_POST['payment_method'];
    $paymentReason = $_POST['payment_reason'] ?? ''; // Use null coalescing operator to handle undefined array key

    // Insert the requisition into the database
    $insertQuery = "INSERT INTO requisitions (requisition_date, requisition_number, requested_by, requester_position, approved_by, approver_position, serial_number, item_name, item_type, payment_method, payment_reason) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($insertQuery);
    
    if ($stmt === false) {
        die("Error preparing statement: " . $conn->error);
    }

    $stmt->bind_param("sssssssssss", $requisitionDate, $requisitionNumber, $requestedBy, $requesterPosition, $approvedBy, $approverPosition, $serialNumber, $itemName, $itemType, $paymentMethod, $paymentReason);
    
    if ($stmt->execute()) {
        // Delete the requested item from the items table
        $deleteQuery = "DELETE FROM items WHERE serial_number = ?";
        $deleteStmt = $conn->prepare($deleteQuery);
        $deleteStmt->bind_param("s", $serialNumber);
        
        if ($deleteStmt->execute()) {
            echo "Requisition submitted successfully and item removed from stock.";
        } else {
            echo "Requisition submitted but failed to remove item from stock: " . $deleteStmt->error;
        }
    } else {
        echo "Error submitting requisition: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Requisition Form</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#serial_number').on('blur', function() {
                var serialNumber = $(this).val();
                $.ajax({
                    url: 'request_item.php',
                    type: 'GET',
                    data: { serial_number: serialNumber },
                    success: function(response) {
                        var data = JSON.parse(response);
                        if (data.error) {
                            $('#item_name').val('');
                            $('#item_type').val('');
                            alert(data.error);
                        } else {
                            $('#item_name').val(data.name);
                            $('#item_type').val(data.type);
                        }
                    }
                });
            });

            $('#payment_method').on('change', function() {
                var paymentMethod = $(this).val();
                if (paymentMethod === 'Not Paid') {
                    $('#payment_reason_div').show();
                } else {
                    $('#payment_reason_div').hide();
                }
            });
        });
    </script>
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h2 class="text-center">Requisition Form</h2>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                            <div class="form-group">
                                <label for="requisition_date">Requisition Date:</label>
                                <input type="date" id="requisition_date" name="requisition_date" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="requisition_number">Requisition Number:</label>
                                <input type="text" id="requisition_number" name="requisition_number" class="form-control" value="<?php echo generateRequisitionNumber(); ?>" readonly>
                            </div>
                            <div class="form-group">
                                <label for="requested_by">Requested By:</label>
                                <input type="text" id="requested_by" name="requested_by" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="requester_position">Requester Position:</label>
                                <input type="text" id="requester_position" name="requester_position" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="approved_by">Approved By:</label>
                                <input type="text" id="approved_by" name="approved_by" class="form-control" value="<?php echo $_SESSION['username']; ?>" readonly>
                            </div>
                            <div class="form-group">
                                <label for="approver_position">Approver Position:</label>
                                <input type="text" id="approver_position" name="approver_position" class="form-control" value="<?php echo $_SESSION['role']; ?>" readonly>
                            </div>
                            <div class="form-group">
                                <label for="serial_number">Serial Number:</label>
                                <input type="text" id="serial_number" name="serial_number" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="item_name">Item Name:</label>
                                <input type="text" id="item_name" name="item_name" class="form-control" readonly>
                            </div>
                            <div class="form-group">
                                <label for="item_type">Item Type:</label>
                                <input type="text" id="item_type" name="item_type" class="form-control" readonly>
                            </div>
                            <div class="form-group">
                                <label for="payment_method">Payment Method:</label>
                                <select id="payment_method" name="payment_method" class="form-control" required>
                                    <option value="">Select Payment Method</option>
                                    <option value="Momo">Mobile Money (Momo)</option>
                                    <option value="Cash">Cash</option>
                                    <option value="Cheque">Cheque</option>
                                    <option value="Not Paid">Not Paid</option>
                                </select>
                            </div>
                            <div class="form-group" id="payment_reason_div" style="display: none;">
                                <label for="payment_reason">Reason for Not Paying:</label>
                                <textarea id="payment_reason" name="payment_reason" class="form-control"></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Submit Requisition</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
