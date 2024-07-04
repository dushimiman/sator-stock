<?php
include('includes/user_nav_bar.php');
?>

<!DOCTYPE html>
<html>
<head>
    <title>Requisition Form</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h2 class="mt-4 mb-4 text-center">Requisition Form</h2>
        <form action="submit_request.php" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="requisition_date">Requisition Date:</label>
                <input type="date" id="requisition_date" name="requisition_date" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="requested_by">Requested By:</label>
                <input type="text" id="requested_by" name="requested_by" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="item_name">Item Name:</label>
                <input type="text" id="item_name" name="item_name" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="quantity">Quantity Needed:</label>
                <input type="number" id="quantity" name="quantity" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="location">Location for Item:</label>
                <input type="text" id="location" name="location" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="payment_description">Payment Description:</label>
                <textarea id="payment_description" name="payment_description" class="form-control"></textarea>
            </div>

            <div class="form-group">
                <label for="reasons">Reasons for Request:</label>
                <textarea id="reasons" name="reasons" class="form-control"></textarea>
            </div>

            <button type="submit" class="btn btn-primary btn-block">Submit</button>
        </form>
    </div>

    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
