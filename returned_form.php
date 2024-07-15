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

// Define item categories and types
$itemTypes = array(
    'SPEED GOVERNORS' => ['SPG 001', 'Only SPG 001 Without Antenna', 'Only SPG 001 Without Display', 'Only SPG 001 Without Antenna and Display', 'R0SCO', 'R0SCO Not Working', 'SG 001 Not Working'],
    'GPS TRACKERS' => ['TK 116', 'TK 119', 'TK 419', 'TK 115', 'MT02S', 'GUT 810G1_Fluel', 'GT06E/teltonika', 'FMB125', 'Gps Not Working/MT02S', 'GPS Not Working'],
    'FUEL LEVER SENSOR' => ['ESCORT', 'FANTOM', 'TTR'],
    'X-1R Product' => ['Engine Treatment (250 ml)', 'Engine Treatment (1L)', 'Jercans ET concentrate', 'Engine Flush', 'Diesel System Treatment', 'Petrol System Treatment', 'Automatic Transmission Treatment', 'Manual Transmission Treatment', 'Octane Booster'],
    'SENSOR FOR ROSCO' => ['SENSOR/ROSCO', 'Long sticker', '60KPH Sticker'],
    'CERTIFICATE PAPER' => ['CERTIFICATE'],
    'CABLE FOR ROSCO' => ['CABLE FOR ROSCO'],
    'Note Book' => ['Note Book'],
    'Remote' => ['Remote'],
    'SIMCARD' => ['SIMCARD'],
    'ENVELOPPE' => ['ENVELOPPE']
);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $itemName = $_POST['item_name'];
    $itemType = $_POST['item_type'];
    $serialNumber = isset($_POST['serial_number']) ? $_POST['serial_number'] : null;
    $returnedBy = $_POST['returned_by'];
    $receivedBy = $_SESSION['username'];
    $returnReason = $_POST['return_reason'];
    $isWorking = isset($_POST['is_working']) ? 1 : 0;
    $imeiNumber = isset($_POST['imei_number']) ? $_POST['imei_number'] : null; // Added IMEI number field

    if (empty($serialNumber)) {
        $insertQuery = "INSERT INTO returned_items (item_name, item_type, serial_number, imei_number, returned_by, received_by, return_reason, is_working) VALUES (?, ?, NULL, ?, ?, ?, ?, ?)";
        $stmtInsert = $conn->prepare($insertQuery);

        if ($stmtInsert === false) {
            die("Error preparing the insert query: " . $conn->error);
        }

        $stmtInsert->bind_param("ssssssi", $itemName, $itemType, $imeiNumber, $returnedBy, $receivedBy, $returnReason, $isWorking);

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

            $insertQuery = "INSERT INTO returned_items (item_name, item_type, serial_number, imei_number, returned_by, received_by, return_reason, is_working) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmtInsert = $conn->prepare($insertQuery);

            if ($stmtInsert === false) {
                die("Error preparing the insert query: " . $conn->error);
            }

            $stmtInsert->bind_param("sssssssi", $itemName, $itemType, $serialNumber, $imeiNumber, $returnedBy, $receivedBy, $returnReason, $isWorking);

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

                // Assuming $branch is defined
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
    <script>
        function populateTypes() {
            var itemName = document.getElementById("item_name").value;
            var itemTypes = <?php echo json_encode($itemTypes); ?>;
            var typeSelect = document.getElementById("item_type");
            typeSelect.innerHTML = '';

            if (itemName in itemTypes) {
                itemTypes[itemName].forEach(function(type) {
                    var option = document.createElement("option");
                    option.text = type;
                    option.value = type;
                    typeSelect.add(option);
                });
            }
            
            // Show/hide IMEI number field based on item type selection
            var imeiField = document.getElementById("imei_number_field");
            if (itemName === "GPS TRACKERS") {
                imeiField.style.display = "block";
            } else {
                imeiField.style.display = "none";
            }
        }
    </script>
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
                                <select id="item_name" name="item_name" class="form-control" onchange="populateTypes()" required>
                                    <option value="">Select Item Name</option>
                                    <?php foreach ($itemTypes as $category => $types): ?>
                                        <option value="<?php echo $category; ?>"><?php echo $category; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="item_type">Item Type:</label>
                                <select id="item_type" name="item_type" class="form-control" required>
                                    <option value="">Select Item Type</option>
                                </select>
                            </div>
                            <div class="form-group" id="imei_number_field" style="display: none;">
                                <label for="imei_number">IMEI Number:</label>
                                <input type="text" id="imei_number" name="imei_number" class="form-control">
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
                                <label for="return_reason">Return Reason:</label>
                                <textarea id="return_reason" name="return_reason" class="form-control" rows="3" required></textarea>
                            </div>
                            <div class="form-group form-check">
                                <input type="checkbox" id="is_working" name="is_working" class="form-check-input">
                                <label class="form-check-label" for="is_working">Is Working?</label>
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
