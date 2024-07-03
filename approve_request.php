<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "stock_management_system";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Approve request
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "UPDATE requests SET status = 'approved' WHERE id = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        die("Error preparing the query: " . $conn->error);
    }

    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "Request approved successfully.";
    } else {
        echo "Error executing the query: " . $stmt->error;
    }

    $stmt->close();
} else {
    echo "No request ID provided.";
}

$conn->close();

header("Location: request_list.php");
?>
