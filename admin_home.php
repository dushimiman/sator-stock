<?php
include('includes/nav_bar.php'); 

session_start();


if (!isset($_SESSION['username'])) {
    header("Location: login.php"); 
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Home</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .card-deck-center {
            /* display: flex; */
            justify-content: center;
            align-items: center;
            height: 100vh; 
        }
    </style>
</head>
<body>
    <div class="container mt-4 card-deck-center">
        <div class="card-deck">
           
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">View All Items</h5>
                    <p class="card-text">View all items in stock currently</p>
                    <a href="items.php" class="btn btn-primary">View Items</a>
                </div>
            </div>
           
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Items Sold</h5>
                    <p class="card-text">View items sold currently</p>
                    <a href="item_sold.php" class="btn btn-primary">View Sold Items</a>
                </div>
            </div>
           
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Items Returned</h5>
                    <p class="card-text">View all items Returned</p>
                    <a href="report.php" class="btn btn-primary">View Returned Items</a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
