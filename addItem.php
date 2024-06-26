<?php
include('includes/nav_bar.php');
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "stock_management_system";

$conn = new mysqli($servername, $username, $password, $dbname);


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $type = $_POST['type'];
    $serial_number = $_POST['serial_number'];
    $price = $_POST['price'];

   
    $stmt = $conn->prepare("INSERT INTO items (name, type, serial_number, price) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sssd", $name, $type, $serial_number, $price);

    if ($stmt->execute()) {
        echo "New record created successfully";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>


<!DOCTYPE html>
<html>
<head>
    <title>Add New Item</title>
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
    <h2>Add New Item</h2>
    <form method="POST" action="add_item.php">
        <label for="item">Item:</label>
        <select id="item" name="name" onchange="updateItemTypes()">
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
        <br><br>
        <label for="type">Item Type:</label>
        <select id="type" name="type"></select>
        <br><br>
        <label for="serial_number">Serial Number:</label>
        <input type="text" id="serial_number" name="serial_number" required>
        <br><br>
        <label for="price">Price:</label>
        <input type="number" step="0.01" id="price" name="price" required>
        <br><br>
        <input type="submit" value="Add Item">
    </form>
</body>
</html>
