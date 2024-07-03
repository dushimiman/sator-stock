<!DOCTYPE html>
<html>
<head>
    <title>Add Item to Stock</title>
    <script>
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

           
            const requiresSerialNumber = ["GPS TRACKERS", "SPEED GOVERNORS"];

            if (requiresSerialNumber.includes(selectedItem)) {
                serialNumberField.style.display = "block";
                quantityField.style.display = "none";
            } else {
                serialNumberField.style.display = "none";
                quantityField.style.display = "block";
            }
        }

        function validateForm() {
            const itemSelect = document.getElementById("item");
            const itemType = itemSelect.value;
            const serialNumber = document.getElementById("serial_number").value;

            
            if (itemType === "GPS TRACKERS") {
                const gpsPattern = /^[A-Z]{2}[0-9]{15}$/;
                if (!gpsPattern.test(serialNumber)) {
                    alert("GPS TRACKERS serial number must be 2 characters, 15 numbers");
                    return false;
                }
            } else if (itemType === "SPEED GOVERNORS") {
                const spgPattern = /^[A-Z]{2}[0-9]{11}$/;
                if (!spgPattern.test(serialNumber)) {
                    alert("SPEED GOVERNORS serial number must be 2 characters, 11 numbers.");
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
    <h2>Add Item to Stock</h2>
    <form method="POST" action="add_item.php" onsubmit="return validateForm()">
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
            <label for="type">Item Type:</label>
            <select id="type" name="type" class="form-control"></select>
        </div>
        <div id="serial_number_field" class="form-group" style="display: none;">
            <label for="serial_number">Serial Number:</label>
            <input type="text" id="serial_number" name="serial_number" class="form-control">
        </div>
        <div id="quantity_field" class="form-group" style="display: none;">
            <label for="quantity">Quantity:</label>
            <input type="number" id="quantity" name="quantity" class="form-control">
        </div>
        <button type="submit" class="btn btn-primary btn-block">Add Item</button>
    </form>
</body>
</html>
<?php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "stock_management_system";


$conn = new mysqli($servername, $username, $password, $dbname);


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


$item_name = isset($_POST['item']) ? $_POST['item'] : null;
$item_type = isset($_POST['type']) ? $_POST['type'] : null;
$serial_number = isset($_POST['serial_number']) ? $_POST['serial_number'] : null;

// Set default quantity based on item type
if ($item_name === "GPS TRACKERS" || $item_name === "SPEED GOVERNORS") {
    $quantity = 1; // Default quantity is 1 for items with serial numbers
} else {
    $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : null;
}

// Validation
if ($item_name === "GPS TRACKERS") {
    if (!preg_match("/^[A-Z]{2}[0-9]{15}$/", $serial_number)) {
        die("GPS TRACKERS serial number must be 2 characters, 15 numbers");
    }
} elseif ($item_name === "SPEED GOVERNORS") {
    if (!preg_match("/^[A-Z]{2}[0-9]{11}$/", $serial_number)) {
        die("SPEED GOVERNORS serial number must be 2 characters, 11 numbers.");
    }
} elseif (is_null($quantity)) {
    die("Quantity is required for non-serial items.");
}


$sql = "INSERT INTO stock (item_name, item_type, serial_number, quantity) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    die("Error preparing the query: " . $conn->error);
}

$stmt->bind_param("sssi", $item_name, $item_type, $serial_number, $quantity);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    echo "Item successfully added to stock.";
} else {
    echo "Error adding item: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>





