<!DOCTYPE html>
<html>
<head>
    <title>Requisition Form</title>
</head>
<body>
    <h2>Requisition Form</h2>
    <form action="submit_request.php" method="post" enctype="multipart/form-data">
        <label for="requisition_date">Requisition Date:</label>
        <input type="date" id="requisition_date" name="requisition_date" required><br><br>

        <label for="requested_by">Requested By:</label>
        <input type="text" id="requested_by" name="requested_by" required><br><br>

        <label for="item_name">Item Name:</label>
        <input type="text" id="item_name" name="item_name" required><br><br>

        <label for="payment_method">Payment Method:</label>
        <select id="payment_method" name="payment_method" onchange="toggleProofOfPayment()">
            <option value="none">None</option>
            <option value="cash">Cash</option>
            <option value="cheque">Cheque</option>
            <option value="momo">Mobile Money</option>
        </select><br><br>

        <div id="proof_of_payment_section" style="display:none;">
        <label for="proof_of_payment_file">Proof of Payment (Upload):</label>
            <input type="file" id="proof_of_payment" name="proof_of_payment" accept=".jpg, .jpeg, .png, .pdf"><br><br>
        </div>

        <div id="reason_if_not_paid_section" style="display:none;">
            <label for="reason_if_not_paid">Reason if not paid:</label>
            <textarea id="reason_if_not_paid" name="reason_if_not_paid"></textarea><br><br>
        </div>

        <input type="submit" value="Submit">
    </form>

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
