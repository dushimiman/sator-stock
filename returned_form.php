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
    $itemName = $_POST['item_name'];
    $serialNumber = isset($_POST['serial_number']) ? $_POST['serial_number'] : null; 
    $returnedBy = $_POST['returned_by'];
    $receivedBy = $_SESSION['username'];
    $returnReason = $_POST['return_reason'];
    $isWorking = isset($_POST['is_working']) ? 1 : 0;

    if (empty($serialNumber)) {
        
        $insertQuery = "INSERT INTO returned_items (item_name, serial_number, returned_by, received_by, return_reason, is_working) VALUES (?, NULL, ?, ?, ?, ?)";
        $stmtInsert = $conn->prepare($insertQuery);

        if ($stmtInsert === false) {
            die("Error preparing the insert query: " . $conn->error);
        }

        $stmtInsert->bind_param("ssssi", $itemName, $returnedBy, $receivedBy, $returnReason, $isWorking);

        if ($stmtInsert->execute()) {
            echo "Item returned successfully.";
        } else {
            echo "Error returning item: " . $stmtInsert->error;
        }
    } else {
       
        $checkQuery = "SELECT * FROM out_in_stock WHERE serial_number = ?";
        $stmtCheck = $conn->prepare($checkQuery);

        if ($stmtCheck === false) {
            die("Error preparing the query: " . $conn->error);
        }

        $stmtCheck->bind_param("s", $serialNumber);
        $stmtCheck->execute();
        $result = $stmtCheck->get_result();

        if ($result->num_rows > 0) {
            $item = $result->fetch_assoc();
            $itemName = $item['item_name'];
            $branch = $item['branch']; 

        
            $insertQuery = "INSERT INTO returned_items (item_name, serial_number, returned_by, received_by, return_reason, is_working) VALUES (?, ?, ?, ?, ?, ?)";
            $stmtInsert = $conn->prepare($insertQuery);

            if ($stmtInsert === false) {
                die("Error preparing the insert query: " . $conn->error);
            }

            $stmtInsert->bind_param("sssssi", $itemName, $serialNumber, $returnedBy, $receivedBy, $returnReason, $isWorking);

            if ($stmtInsert->execute()) {
               
                $deleteQuery = "DELETE FROM out_in_stock WHERE serial_number = ?";
                $stmtDelete = $conn->prepare($deleteQuery);

                if ($stmtDelete === false) {
                    die("Error preparing the delete query: " . $conn->error);
                }

                $stmtDelete->bind_param("s", $serialNumber);
                $stmtDelete->execute();

                
                $insertStockQuery = "INSERT INTO stock (item_name, serial_number, branch, creation_date, quantity, status) VALUES (?, ?, ?, NOW(), 1, ?)";
                $status = $isWorking ? 'returned but working' : 'returned but not working';
                $stmtInsertStock = $conn->prepare($insertStockQuery);

                if ($stmtInsertStock === false) {
                    die("Error preparing the stock insert query: " . $conn->error);
                }

                $stmtInsertStock->bind_param("ssss", $itemName, $serialNumber, $branch, $status);
                $stmtInsertStock->execute();

                echo "Item returned successfully.";
            } else {
                echo "Error returning item: " . $stmtInsert->error;
            }
        } else {
            echo "Serial number not found in out_in_stock table.";
        }
    }
}

$conn->close();
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
            padding: 1.25rem; 
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
                                <label for="item_name">Item Name:</label>
                                <input type="text" id="item_name" name="item_name" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="serial_number">S/N:</label>
                                <input type="text" id="serial_number" name="serial_number" class="form-control">
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
                            <div class="form-group">
                                <label for="is_working">Is Working:</label>
                                <input type="checkbox" id="is_working" name="is_working" class="form-control">
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
