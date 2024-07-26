
<?php
session_start();
include(__DIR__ . '/../includes/nav_bar.php');
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    header("Location: ../login.php"); 
    exit();
}


?>

<!DOCTYPE html>
<html>
<head>
    <title>User Home</title>
    <link rel="icon" href="./images/stock-icon.png" type="image/x-icon"> 
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <h2 class="text-center mt-5">User Home</h2>
                <div class="list-group mt-4">
                    <a href="../requests/request_form.php" class="list-group-item list-group-item-action">Request Form</a>
                    <a href="../requests/user_request.php" class="list-group-item list-group-item-action">View Your Requisitions</a>
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
