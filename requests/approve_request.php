<?php
session_start();
include(__DIR__ . '/../includes/nav_bar.php');
include(__DIR__ . '/../includes/db.php'); 
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    header("Location: ../login.php"); 
    exit();
}
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['request_id'])) {
        $request_id = $_POST['request_id'];
        $sql = "UPDATE requests SET status = 'approved' WHERE id = ?";
        $stmt = $mysqli->prepare($sql);

        if ($stmt === false) {
            die("Error preparing the query: " . $mysqli->error);
        }

        $stmt->bind_param("i", $request_id);

        if ($stmt->execute()) {
            echo "Request approved successfully.";
        } else {
            echo "Error executing the query: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "Request ID not provided.";
    }
} else {
    echo "Invalid request method.";
}

$mysqli->close();

header("Location: request_list.php");
exit();
?>
