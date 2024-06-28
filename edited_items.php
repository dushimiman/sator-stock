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


$query = "SELECT id, name, type, serial_number,  last_edited_by FROM items";

$result = $conn->query($query);

if ($result === false) {
    die("Error fetching items: " . $conn->error);
}

if ($result->num_rows > 0) {
    echo "<div class='table-responsive'>";
    echo "<h2 class='my-4'>Edited Items</h2>";
    echo "<table class='table table-striped'>";
    echo "<thead>";
    echo "<tr>";
    echo "<th>ID</th>";
    echo "<th>Name</th>";
    echo "<th>Type</th>";
    echo "<th>Serial Number</th>";
   
    echo "<th>Last Edited By</th>";
    echo "</tr>";
    echo "</thead>";
    echo "<tbody>";

    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . $row['name'] . "</td>";
        echo "<td>" . $row['type'] . "</td>";
        echo "<td>" . $row['serial_number'] . "</td>";
        
        echo "<td>" . $row['last_edited_by'] . "</td>";
        echo "</tr>";
    }

    echo "</tbody>";
    echo "</table>";
    echo "</div>"; 
} else {
    echo "<p class='mt-4'>No edited items found.</p>";
}

$conn->close();
?>
