<?php
include(__DIR__ . '/../includes/nav_bar.php');
include(__DIR__ . '/../includes/db.php'); // Ensure this initializes $mysqli

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $item_name = $_POST['item'];
    $item_type = $_POST['type'];
    $serial_number = isset($_POST['serial_number']) ? $_POST['serial_number'] : null;
    $imei = isset($_POST['imei']) ? $_POST['imei'] : null;
    $quantity = isset($_POST['quantity']) ? $_POST['quantity'] : null;

    $creation_date = date("Y-m-d H:i:s");

    if ($item_name == "SPEED GOVERNORS") {
        if ($item_type == "SPG 001") {
            if (!empty($serial_number) && !preg_match('/^[A-Za-z]{2}\d{12}$/', $serial_number)) {
                die('Serial number for SPG 001 must be 2 letters followed by 12 numbers.');
            }
        } else {
            if (!empty($serial_number) && !preg_match('/^\d{15}$/', $serial_number)) {
                die('Serial number for other SPEED GOVERNORS must be exactly 15 numbers.');
            }
        }
    }

    if ($item_name == "GPS TRACKERS") {
        if (!empty($serial_number) && !preg_match('/^\d{6}$/', $serial_number)) {
            die('Serial number must be exactly 6 numbers.');
        }
        if (empty($imei) || !preg_match('/^\d{15}$/', $imei)) {
            die('IMEI is required and must be exactly 15 numbers.');
        }
    }

    if (!empty($serial_number)) {
        $quantity = 1;
    } else {
        $serial_number = null; 
    }

    $query = "INSERT INTO stock (item_name, item_type, serial_number, imei, quantity, creation_date) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $mysqli->prepare($query);

    if ($stmt === false) {
        die('Prepare failed: ' . htmlspecialchars($mysqli->error));
    }

    $bind = $stmt->bind_param("ssssis", $item_name, $item_type, $serial_number, $imei, $quantity, $creation_date);

    if ($bind === false) {
        die('Bind param failed: ' . htmlspecialchars($stmt->error));
    }

    $execute = $stmt->execute();

    if ($execute === false) {
        die('Execute failed: ' . htmlspecialchars($stmt->error));
    }

    header("Location: view_all_item.php");
    exit;

    $stmt->close();
    $mysqli->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Item to Stock</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .container {
            margin-top: 20px; /* Adjust the margin value as needed */
        }
        .form-container {
            max-width: 600px; 
            margin: auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            background-color: #fff;
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        // JavaScript functions for form interaction
        const itemTypes = {
            'SPEED GOVERNORS': ['SPG 001', 'Only SPG 001 Without Antenna', 'Only SPG 001 Without Display', 'Only SPG 001 Without Antenna and Display', 'R0SCO', 'R0SCO Not Working', 'SG 001 Not Working'],
            'GPS TRACKERS': ['TK 116', 'TK 119', 'TK 419', 'TK 115', 'MT02S', 'GUT 810G1_Fluel', 'GT06E/teltonika', 'FMB125', 'Gps Not Working/MT02S', 'GPS Not Working'],
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

        function updateForm() {
            const itemSelect = document.getElementById("item");
            const typeSelect = document.getElementById("type");
            const serialNumberField = document.getElementById("serial_number_field");
            const imeiField = document.getElementById("imei_field");
            const quantityField = document.getElementById("quantity_field");
            const selectedItem = itemSelect.value;

            typeSelect.innerHTML = '';
            if (itemTypes[selectedItem]) {
                itemTypes[selectedItem].forEach(type => {
                    const option = document.createElement('option');
                    option.value = type;
                    option.text = type;
                    typeSelect.add(option);
                });
            }

            if (selectedItem === "GPS TRACKERS" || selectedItem === "SPEED GOVERNORS") {
                serialNumberField.style.display = "block";
                imeiField.style.display = selectedItem === "GPS TRACKERS" ? "block" : "none";
                quantityField.style.display = "none";
            } else {
                serialNumberField.style.display = "none";
                imeiField.style.display = "none";
                quantityField.style.display = "block";
            }
        }

        function validateForm() {
            const itemSelect = document.getElementById("item");
            const itemType = itemSelect.value;
            const serialNumber = document.getElementById("serial_number").value;
            const imei = document.getElementById("imei").value;
            const itemTypeSelected = document.getElementById("type").value;

            if (itemType === "SPEED GOVERNORS") {
                if (itemTypeSelected === "SPG 001" && serialNumber && !/^[A-Za-z]{2}\d{12}$/.test(serialNumber)) {
                    alert('Serial number for SPG 001 must be 2 letters followed by 12 numbers.');
                    return false;
                } else if (itemTypeSelected !== "SPG 001" && serialNumber && !/^\d{15}$/.test(serialNumber)) {
                    alert('Serial number for other SPEED GOVERNORS must be exactly 15 numbers.');
                    return false;
                }
            }

            if (itemType === "GPS TRACKERS") {
                if (serialNumber && !/^\d{6}$/.test(serialNumber)) {
                    alert('Serial number must be exactly 6 numbers.');
                    return false;
                }
                if (!imei || !/^\d{15}$/.test(imei)) {
                    alert('IMEI is required and must be exactly 15 numbers.');
                    return false;
                }
            }

            return true;
        }

        window.onload = function() {
            updateForm();
        }
    </script>
</head>
<body>
    <div class="container">
        <div class="form-container">
            <h2 class="mt-4 mb-4 text-center">Add Item to Stock</h2>
            <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" onsubmit="return validateForm()">
                <div class="form-group">
                    <label for="item">Item:</label>
                    <select id="item" name="item" onchange="updateForm()" class="form-control">
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
                    <label for="type">Type:</label>
                    <select id="type" name="type" class="form-control">
                        <!-- Options will be added here based on selected item -->
                    </select>
                </div>

                <div id="serial_number_field" class="form-group">
                    <label for="serial_number">Serial Number:</label>
                    <input type="text" id="serial_number" name="serial_number" class="form-control">
                </div>

                <div id="imei_field" class="form-group">
                    <label for="imei">IMEI:</label>
                    <input type="text" id="imei" name="imei" class="form-control">
                </div>

                <div id="quantity_field" class="form-group">
                    <label for="quantity">Quantity:</label>
                    <input type="number" id="quantity" name="quantity" class="form-control">
                </div>

                <button type="submit" class="btn btn-primary">Add Item</button>
            </form>
        </div>
    </div>
</body>
</html>
