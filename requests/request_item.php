<?php
session_start();
include('../db.php');

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $item_name = $_POST['item_name'];
    $requested_by = $_POST['requested_by'];
    $received_by = $_POST['received_by'];
    $reasons = $_POST['reasons'];

    // Check if the item exists in the database
    $sql = "SELECT * FROM items WHERE name='$item_name'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $item = $result->fetch_assoc();
        
        // Check if item is in stock
        if ($item['quantity'] > 0) {
            // Item is in stock, proceed with request
            $new_quantity = $item['quantity'] - 1;
            $update_sql = "UPDATE items SET quantity='$new_quantity' WHERE name='$item_name'";
            $conn->query($update_sql);

            // Insert request details into requests table
            $insert_sql = "INSERT INTO requests (item_name, requested_by, received_by, reasons) VALUES ('$item_name', '$requested_by', '$received_by', '$reasons')";
            $conn->query($insert_sql);

            // Generate PDF
            require('../fpdf.php');

            class PDF extends FPDF {
                function Header() {
                    $this->SetFont('Arial', 'B', 12);
                    $this->Cell(0, 10, 'Item Request Form', 0, 1, 'C');
                }
            }

            $pdf = new PDF();
            $pdf->AddPage();
            $pdf->SetFont('Arial', '', 12);
            $pdf->Cell(0, 10, "Item Name: $item_name", 0, 1);
            $pdf->Cell(0, 10, "Requested By: $requested_by", 0, 1);
            $pdf->Cell(0, 10, "Received By: $received_by", 0, 1);
            $pdf->Cell(0, 10, "Reasons: $reasons", 0, 1);

            // Output PDF directly to the browser with filename 'request_form.pdf'
            $pdf->Output('D', 'request_form.pdf');

            // Set success message in session
            $_SESSION['message'] = "Request successful. The PDF has been downloaded.";
        } else {
            // Item is out of stock
            $_SESSION['message'] = "The requested item is not in stock.";
        }
    } else {
        // Item does not exist
        $_SESSION['message'] = "The requested item does not exist.";
    }

    // Redirect to the form page after processing
    header("Location: request_item.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Request Item</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-4">
        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-info">
                <?php
                echo $_SESSION['message'];
                unset($_SESSION['message']);
                ?>
            </div>
        <?php endif; ?>
        <h1>Request Item</h1>
        <form method="post" action="request_item.php">
            <div class="form-group">
                <label for="item_name">Item Name</label>
                <input type="text" class="form-control" id="item_name" name="item_name" required>
            </div>
            <div class="form-group">
                <label for="requested_by">Requested By</label>
                <input type="text" class="form-control" id="requested_by" name="requested_by" required>
            </div>
            <div class="form-group">
                <label for="received_by">Received By</label>
                <input type="text" class="form-control" id="received_by" name="received_by" required>
            </div>
            <div class="form-group">
                <label for="reasons">Reasons</label>
                <textarea class="form-control" id="reasons" name="reasons" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Submit Request</button>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
