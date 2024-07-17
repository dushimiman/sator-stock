<?php
require_once('TCPDF-main/tcpdf.php');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "stock_management_system";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date("Y-m-d");
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date("Y-m-d");

$sql_all_stock = "SELECT item_name, item_type, SUM(quantity) AS total_quantity 
                  FROM stock 
                  GROUP BY item_name, item_type";
$result_all_stock = $conn->query($sql_all_stock);

$sql_stock_daily = "SELECT item_type, SUM(quantity) AS total_quantity 
                    FROM stock 
                    WHERE DATE(creation_date) BETWEEN '$start_date' AND '$end_date'
                    GROUP BY item_type";
$result_stock_daily = $conn->query($sql_stock_daily);

$sql_requests_daily = "SELECT item_name, SUM(quantity) AS total_quantity, status 
                       FROM requests 
                       WHERE DATE(requisition_date) BETWEEN '$start_date' AND '$end_date'
                       GROUP BY item_name, status";
$result_requests_daily = $conn->query($sql_requests_daily);

$sql_return_daily = "SELECT item_name, returned_by, return_reason 
                     FROM returned_items 
                     WHERE DATE(returned_date) BETWEEN '$start_date' AND '$end_date'";
$result_return_daily = $conn->query($sql_return_daily);

$sql_out_in_stock_daily = "SELECT item_name, SUM(quantity) AS total_quantity 
                           FROM out_in_stock 
                           WHERE DATE(created_at) BETWEEN '$start_date' AND '$end_date'
                           GROUP BY item_name";
$result_out_in_stock_daily = $conn->query($sql_out_in_stock_daily);

$conn->close();

$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Sator Rwanda Ltd'); 
$pdf->SetTitle('Stock Report');
$pdf->SetSubject('Stock Report');
$pdf->SetKeywords('TCPDF, PDF, stock, report');


$pdf->SetHeaderData('logo.png', 30, 'Stock Management System', "Report Date Range: $start_date to $end_date");


$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);


$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// Set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// Set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// Set font
$pdf->SetFont('dejavusans', '', 10);

// Add a page
$pdf->AddPage();

// Title
$pdf->Cell(0, 10, 'Stock Report', 0, 1, 'C');

// Items in Stock (All)
$pdf->Ln(5);
$pdf->SetFont('dejavusans', 'B', 12);
$pdf->Cell(0, 10, 'Items in Stock (All)', 0, 1, 'L');
$pdf->SetFont('dejavusans', '', 10);
if ($result_all_stock && $result_all_stock->num_rows > 0) {
    while ($row = $result_all_stock->fetch_assoc()) {
        $pdf->Cell(0, 10, $row["item_type"] . ': ' . $row["total_quantity"], 0, 1);
    }
} else {
    $pdf->Cell(0, 10, 'No items in stock found.', 0, 1);
}

// Items in Stock (Daily)
$pdf->Ln(5);
$pdf->SetFont('dejavusans', 'B', 12);
$pdf->Cell(0, 10, 'Items in Stock (Daily)', 0, 1, 'L');
$pdf->SetFont('dejavusans', '', 10);
if ($result_stock_daily && $result_stock_daily->num_rows > 0) {
    while ($row = $result_stock_daily->fetch_assoc()) {
        $pdf->Cell(0, 10, $row["item_type"] . ': ' . $row["total_quantity"], 0, 1);
    }
} else {
    $pdf->Cell(0, 10, 'No items in stock found for the selected date range.', 0, 1);
}

// Requests for Date Range
$pdf->Ln(5);
$pdf->SetFont('dejavusans', 'B', 12);
$pdf->Cell(0, 10, 'Requests for the Selected Date Range', 0, 1, 'L');
$pdf->SetFont('dejavusans', '', 10);
if ($result_requests_daily && $result_requests_daily->num_rows > 0) {
    while ($row = $result_requests_daily->fetch_assoc()) {
        $pdf->Cell(0, 10, $row["item_name"] . ': ' . $row["total_quantity"] . ' (' . $row["status"] . ')', 0, 1);
    }
} else {
    $pdf->Cell(0, 10, 'No requests found for the selected date range.', 0, 1);
}

// Items Returned for Date Range
$pdf->Ln(5);
$pdf->SetFont('dejavusans', 'B', 12);
$pdf->Cell(0, 10, 'Items Returned for the Selected Date Range', 0, 1, 'L');
$pdf->SetFont('dejavusans', '', 10);
if ($result_return_daily && $result_return_daily->num_rows > 0) {
    while ($row = $result_return_daily->fetch_assoc()) {
        $pdf->Cell(0, 10, $row["item_name"] . ': Returned by ' . $row["returned_by"] . ' (' . $row["return_reason"] . ')', 0, 1);
    }
} else {
    $pdf->Cell(0, 10, 'No items returned found for the selected date range.', 0, 1);
}

// Items Out in Stock for Date Range
$pdf->Ln(5);
$pdf->SetFont('dejavusans', 'B', 12);
$pdf->Cell(0, 10, 'Items Out in Stock for the Selected Date Range', 0, 1, 'L');
$pdf->SetFont('dejavusans', '', 10);
if ($result_out_in_stock_daily && $result_out_in_stock_daily->num_rows > 0) {
    while ($row = $result_out_in_stock_daily->fetch_assoc()) {
        $pdf->Cell(0, 10, $row["item_name"] . ': ' . $row["total_quantity"], 0, 1);
    }
} else {
    $pdf->Cell(0, 10, 'No items out in stock found for the selected date range.', 0, 1);
}

// Close and output PDF document
$pdf->Output('stock_report.pdf', 'I');
?>
