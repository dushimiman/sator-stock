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


$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date("Y-m-01");
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date("Y-m-t");


$sql_all_stock = "SELECT item_name, item_type, SUM(quantity) AS total_quantity 
                  FROM stock 
                  WHERE DATE(creation_date) BETWEEN '$start_date' AND '$end_date'
                  GROUP BY item_name, item_type";
$result_all_stock = $conn->query($sql_all_stock);


$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);


$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Stock Management System');
$pdf->SetTitle('Monthly Stock Report');
$pdf->SetSubject('Monthly Stock Report');
$pdf->SetKeywords('TCPDF, PDF, stock, report');


$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, 'Monthly Stock Report', "Report Date Range: $start_date to $end_date");


$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));


$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);


$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

$pdf->SetFont('helvetica', '', 10);

$pdf->AddPage();

$pdf->Cell(0, 10, 'Monthly Stock Report', 0, 1, 'C');

$pdf->Ln(5);
$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(0, 10, 'Items in Stock (Monthly)', 0, 1, 'L');
$pdf->SetFont('helvetica', '', 10);
if ($result_all_stock && $result_all_stock->num_rows > 0) {
    while ($row = $result_all_stock->fetch_assoc()) {
        $pdf->Cell(0, 10, $row["item_name"] . ' - ' . $row["item_type"] . ': ' . $row["total_quantity"], 0, 1);
    }
} else {
    $pdf->Cell(0, 10, 'No items in stock found for the selected month.', 0, 1);
}


$pdf->Output('monthly_stock_report.pdf', 'I');


$conn->close();
?>
