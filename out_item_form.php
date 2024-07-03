<?php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "stock_management_system";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT * FROM requests WHERE id = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        die("Error preparing the query: " . $conn->error);
    }

    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $request = $result->fetch_assoc();
    $stmt->close();
} else {
    die("No request ID provided.");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Out Item in Stock</title>
</head>
<body>
    <h2>Out Item in Stock</h2>
    <form action="out_item_submit.php" method="post">
        <input type="hidden" name="request_id" value="<?php echo $request['id']; ?>">
        <label for="item_name">Item Name:</label>
        <input type="text" id="item_name" name="item_name" value="<?php echo $request['item_name']; ?>" readonly><br><br>

        <div id="serial_number_section" style="display: none;">
            <label for="serial_number">Serial Number:</label>
            <input type="text" id="serial_number" name="serial_number"><br><br>
        </div>

        <div id="quantity_section" style="display: none;">
            <label for="quantity">Quantity:</label>
            <input type="number" id="quantity" name="quantity"><br><br>
        </div>

        <input type="submit" value="Out Item">
    </form>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var itemName = "<?php echo $request['item_name']; ?>";
            var serialNumberSection = document.getElementById("serial_number_section");
            var quantitySection = document.getElementById("quantity_section");

            if (itemName === "GPS TRACKERS" || itemName === "SPEED GOVERNORS") {
                serialNumberSection.style.display = "block";
            } else {
                quantitySection.style.display = "block";
            }
        });
    </script>
</body>
</html>

<?php
$conn->close();
?>
