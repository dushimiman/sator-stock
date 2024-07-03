
<?php
include('includes/user_nav_bar.php');
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] < 2) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Home</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <h2 class="text-center mt-5">User Home</h2>
                <div class="list-group mt-4">
                    <a href="request_form.php" class="list-group-item list-group-item-action">Request Form</a>
                    <a href="view_requisitions.php" class="list-group-item list-group-item-action">View Your Requisitions</a>
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
