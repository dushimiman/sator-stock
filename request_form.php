<?php
include('includes/nav_bar.php');
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
                <label for="payment_method">Payment Method:</label>
                <select id="payment_method" name="payment_method" class="form-control" onchange="toggleProofOfPayment()">
                    <option value="none">None</option>
                    <option value="cash">Cash</option>
                    <option value="cheque">Cheque</option>
                    <option value="momo">Mobile Money</option>
                </select>
            </div>

            <div id="proof_of_payment_section" class="form-group" style="display:none;">
                <label for="proof_of_payment_file">Proof of Payment (Upload):</label>
                <input type="file" id="proof_of_payment" name="proof_of_payment" class="form-control-file" accept=".jpg, .jpeg, .png, .pdf">
            </div>

            <div id="reason_if_not_paid_section" class="form-group" style="display:none;">
                <label for="reason_if_not_paid">Reason if not paid:</label>
                <textarea id="reason_if_not_paid" name="reason_if_not_paid" class="form-control"></textarea>
            </div>

            <button type="submit" class="btn btn-primary btn-block">Submit</button>
        </form>
    </div>

    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        function toggleProofOfPayment() {
            var paymentMethod = document.getElementById('payment_method').value;
            var proofSection = document.getElementById('proof_of_payment_section');
            var reasonSection = document.getElementById('reason_if_not_paid_section');
            
            if (paymentMethod === 'none') {
                proofSection.style.display = 'none';
                reasonSection.style.display = 'block';
            } else {
                proofSection.style.display = 'block';
                reasonSection.style.display = 'none';
            }
        }
    </script>
</body>
</html>
