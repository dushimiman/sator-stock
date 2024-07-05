<?php
require('fpdf/fpdf.php');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "stock_management_system";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

function generateReport($conn, $startDate, $endDate) {
    // Fetch items in stock
    $stockQuery = "SELECT item_name, item_type, SUM(quantity) as total_quantity FROM stock GROUP BY item_name, item_type";
    $stockResult = $conn->query($stockQuery);
    $stockItems = $stockResult->fetch_all(MYSQLI_ASSOC);

    // Fetch items requested
    $requestedQuery = "SELECT * FROM requisitions WHERE requisition_date BETWEEN ? AND ?";
    $stmtRequested = $conn->prepare($requestedQuery);
    $stmtRequested->bind_param("ss", $startDate, $endDate);
    $stmtRequested->execute();
    $requestedResult = $stmtRequested->get_result();
    $requestedItems = $requestedResult->fetch_all(MYSQLI_ASSOC);

    // Fetch items out in stock
    $outStockQuery = "SELECT * FROM out_in_stock WHERE out_date BETWEEN ? AND ?";
    $stmtOutStock = $conn->prepare($outStockQuery);
    $stmtOutStock->bind_param("ss", $startDate, $endDate);
    $stmtOutStock->execute();
    $outStockResult = $stmtOutStock->get_result();
    $outStockItems = $outStockResult->fetch_all(MYSQLI_ASSOC);

    // Fetch items returned
    $returnedQuery = "SELECT * FROM returned_items WHERE return_date BETWEEN ? AND ?";
    $stmtReturned = $conn->prepare($returnedQuery);
    $stmtReturned->bind_param("ss", $startDate, $endDate);
    $stmtReturned->execute();
    $returnedResult = $stmtReturned->get_result();
    $returnedItems = $returnedResult->fetch_all(MYSQLI_ASSOC);

    return [
        'stockItems' => $stockItems,
        'requestedItems' => $requestedItems,
        'outStockItems' => $outStockItems,
        'returnedItems' => $returnedItems,
    ];
}

if (isset($_GET['start_date']) && isset($_GET['end_date'])) {
    $startDate = $_GET['start_date'];
    $endDate = $_GET['end_date'];
    $reportData = generateReport($conn, $startDate, $endDate);

    class PDF extends FPDF {
        function Header() {
            $this->SetFont('Arial', 'B', 12);
            $this->Cell(0, 10, 'Stock Report', 0, 1, 'C');
        }

        function Footer() {
            $this->SetY(-15);
            $this->SetFont('Arial', 'I', 8);
            $this->Cell(0, 10, 'Page ' . $this->PageNo(), 0, 0, 'C');
        }

        function ReportTable($header, $data) {
            $this->SetFont('Arial', 'B', 12);
            foreach ($header as $col) {
                $this->Cell(40, 7, $col, 1);
            }
            $this->Ln();
            $this->SetFont('Arial', '', 12);
            foreach ($data as $row) {
                foreach ($row as $col) {
                    $this->Cell(40, 6, $col, 1);
                }
                $this->Ln();
            }
        }
    }

    $pdf = new PDF();
    $pdf->AddPage();

    // Items in Stock
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->Cell(0, 10, 'Items in Stock', 0, 1, 'L');
    $pdf->SetFont('Arial', '', 12);
    $header = ['Item Name', 'Item Type', 'Total Quantity'];
    $data = array_map(function ($item) {
        return [$item['item_name'], $item['item_type'], $item['total_quantity']];
    }, $reportData['stockItems']);
    $pdf->ReportTable($header, $data);

    // Items Requested
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->Cell(0, 10, 'Items Requested', 0, 1, 'L');
    $pdf->SetFont('Arial', '', 12);
    $header = ['ID', 'Serial Number', 'Requested By', 'Request Date'];
    $data = array_map(function ($item) {
        return [$item['id'], $item['serial_number'], $item['requested_by'], $item['requisition_date']];
    }, $reportData['requestedItems']);
    $pdf->ReportTable($header, $data);

    // Items Out in Stock
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->Cell(0, 10, 'Items Out in Stock', 0, 1, 'L');
    $pdf->SetFont('Arial', '', 12);
    $header = ['ID', 'Serial Number', 'Out Date'];
    $data = array_map(function ($item) {
        return [$item['id'], $item['serial_number'], $item['out_date']];
    }, $reportData['outStockItems']);
    $pdf->ReportTable($header, $data);

    // Items Returned
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->Cell(0, 10, 'Items Returned', 0, 1, 'L');
    $pdf->SetFont('Arial', '', 12);
    $header = ['ID', 'Serial Number', 'Returned By', 'Received By', 'Return Reason', 'Return Date', 'Is Working'];
    $data = array_map(function ($item) {
        return [
            $item['id'],
            $item['serial_number'],
            $item['returned_by'],
            $item['received_by'],
            $item['return_reason'],
            $item['return_date'],
            $item['is_working']
        ];
    }, $reportData['returnedItems']);
    $pdf->ReportTable($header, $data);

    $pdf->Output('D', 'stock_report.pdf');
}

$conn->close();
?>
