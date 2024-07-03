<?php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "stock_management_system";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $requisition_date = $_POST['requisition_date'];
    $requested_by = $_POST['requested_by'];
    $item_name = $_POST['item_name'];
    $payment_method = $_POST['payment_method'];
    $reason_if_not_paid = $_POST['reason_if_not_paid'];
    if ($payment_method !== 'none') {
        
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["proof_of_payment"]["name"]);
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

        // Check file size and format
        if ($_FILES["proof_of_payment"]["size"] > 500000) {
            echo "Sorry, your file is too large.";
            $uploadOk = 0;
        }
        if($imageFileType != "jpg" && $imageFileType != "jpeg" && $imageFileType != "png" && $imageFileType != "pdf") {
            echo "Sorry, only JPG, JPEG, PNG & PDF files are allowed.";
            $uploadOk = 0;
        }

        
        if ($_FILES['proof_of_payment_file']['error'] === UPLOAD_ERR_OK) {
            $file_name = $_FILES['proof_of_payment_file']['name'];
            $file_tmp = $_FILES['proof_of_payment_file']['tmp_name'];
            $file_destination = 'proofs/' . $file_name;
        
            if (move_uploaded_file($file_tmp, $file_destination)) {
              
                $proof_of_payment_file = $file_name;
            } else {
               
            }
        } else {
           
        }
    }        

$date = new DateTime($requisition_date);
$year = $date->format('Y');
$month = $date->format('m');
$day = $date->format('d');

$sql = "SELECT COUNT(*) AS count FROM requests WHERE YEAR(requisition_date) = $year AND MONTH(requisition_date) = $month AND DAY(requisition_date) = $day";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$count = $row['count'] + 1;
$requisition_number = "$year-$month-$day-$count";


$sql = "INSERT INTO requests (requisition_date, requisition_number, requested_by, item_name, payment_method, proof_of_payment, reason_if_not_paid) VALUES (?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssssss", $requisition_date, $requisition_number, $requested_by, $item_name, $payment_method, $proof_of_payment, $reason_if_not_paid);

if ($stmt->execute()) {
    echo "Request submitted successfully.";
    header("Location: request_list.php");
} else {
    echo "Error: " . $stmt->error;
}
}

$stmt->close();
$conn->close();
?>
