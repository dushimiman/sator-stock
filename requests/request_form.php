<?php include('includes/user_nav_bar.php'); ?>

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
                <select id="item_name" name="item_name" class="form-control" onchange="showOtherItemInput()" required>
                    <option value="">Select Item Name</option>
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
                    <option value="Other">Other</option>
                </select>
            </div>

            <div class="form-group" id="other_item_container" style="display: none;">
                <label for="other_item">Other Item:</label>
                <input type="text" id="other_item" name="other_item" class="form-control">
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

    <script>
        function showOtherItemInput() {
            const itemName = document.getElementById('item_name').value;
            const otherItemContainer = document.getElementById('other_item_container');

            otherItemContainer.style.display = itemName === 'Other' ? 'block' : 'none';
        }
    </script>

    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
