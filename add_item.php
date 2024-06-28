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

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $type = $_POST['type'];
    $serial_number = $_POST['serial_number'];
    $branch = "HQS"; 

    $stmt = $conn->prepare("INSERT INTO items (name, type, serial_number, creation_date) VALUES (?, ?, ?, CURRENT_TIMESTAMP)");
    if ($stmt === false) {
        die("Error preparing statement: " . $conn->error);
    }
    $stmt->bind_param("sss", $name, $type, $serial_number);

    if ($stmt->execute()) {
        echo "New item added successfully.<br>";
    } else {
        echo "Error executing statement: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Item</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        const itemTypes = {
            'SPEED GOVERNORS': ['SPG 001', 'Only SPG 001 Without Antenna', 'Only SPG 001 Without Display', 'Only SPG 001 Without Antenna and Display', 'R0SCO', 'R0SCO Not Working', 'SG 001 Not Working'],
            'GPS TRACKERS': ['TK 116', 'TK 119', 'TK419', 'TK 115', 'MT02S', 'GUT 810G1_Fluel', 'GT06E/ teltonika', 'FMB125', 'Gps Not Working/ Mt02S', 'GPS Not Working'],
            'FUEL LEVER SENSOR': ['ESCORT', 'FANTOM', 'TTR'],
            'X-1R Product': ['Engine Treatment (250 ml)', 'Engine Treatment (1L)', 'Jercans ET concentrate', 'Engine Flush', 'Diesel System Treatment', 'Petrol System Treatment', 'Automatic Transmission Treatment', 'Manual Transmission Treatment', 'Octane Booster'],
            'SENSOR FOR ROSCO': ['SENSOR/ROSCO', 'Long sticker', '60KPH Sticker'],
            'CERTIFICATE PAPER': ['CERTIFICATE'],
            'CABLE FOR ROSCO': ['CABLE FOR ROSCO'],
            'Note Book': ['Note Book'],
            'Remote': ['Remote'],
            'SIMCARD': ['SIMCARD'],
            'ENVELOPPE': ['ENVELOPPE']
        };

        function updateItemTypes() {
            const itemSelect = document.getElementById('item');
            const typeSelect = document.getElementById('type');
            const selectedItem = itemSelect.value;

            while (typeSelect.options.length > 0) {
                typeSelect.remove(0);
            }

            if (itemTypes[selectedItem]) {
                itemTypes[selectedItem].forEach(type => {
                    const option = document.createElement('option');
                    option.value = type;
                    option.text = type;
                    typeSelect.add(option);
                });
            }
        }

        window.onload = function() {
            updateItemTypes();
        }
    </script>
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h2 class="text-center">Add New Item</h2>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="add_item.php">
                            <div class="form-group">
                                <label for="item">Item:</label>
                                <select id="item" name="name" onchange="updateItemTypes()" class="form-control">
                                    <option value="SPEED GOVERNORS">SPEED GOVERNORS</option>
                                    <option value="GPS TRACKERS">GPS TRACKERS</option>
                                    <option value="FUEL LEVER SENSOR">FUEL LEVER SENSOR</option>
                                    <option value="X-1R Product">X-1R Product</option>
                                    <option value="SENSOR FOR ROSCO">SENSOR FOR ROSCO</option>
                                    <option value="CERTIFICATE PAPER">CERTIFICATE PAPER</option>
                                    <option value="CABLE FOR ROSCO">CABLE FOR ROSCO</option>
                                    <option value="Note Book">Note Book</option>
                                    <option value="Remote">Remote</option>
                                    <option value="SIMCARD">SIMCARD</option>
                                    <option value="ENVELOPPE">ENVELOPPE</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="type">Item Type:</label>
                                <select id="type" name="type" class="form-control"></select>
                            </div>
                            <div class="form-group">
                                <label for="serial_number">Serial Number:</label>
                                <input type="text" id="serial_number" name="serial_number" class="form-control" required>
                            </div>
                            <button type="submit" class="btn btn-primary btn-block">Add Item</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
