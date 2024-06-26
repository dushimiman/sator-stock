<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "sock_management_system";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$categories = [
    'GPS TRACKERS' => [
        'TK 116', 'TK 119', 'TK419', 'TK 115', 'MT02S', 'GUT 810G1_Fluel',
        'GT06E/ teltonika', 'FMB125', 'GPS Not Working/ Mt02S', 'GPS Not Working'
    ],
    'SPEED GOVERNORS' => [
        'SPG 001', 'Only SPG 001 Without Antenna', 'Only SPG 001 Without Display',
        'Only SPG 001 Without Antenna and Display', 'R0SCO', 'R0SCO Not Working', 'SG 001 Not Working'
    ],
    'FUEL LEVER SENSOR' => ['ESCORT', 'FANTOM', 'TTR'],
    'SENSOR FOR ROSCO' => ['SENSOR/ROSCO', 'Long sticker', '60KPH Sticker'],
    'CERTIFICATE PAPER' => ['certificate'],
    'X-1R Product' => [
        'Engine Treatment (250 ml)', 'Engine Treatment (1L)', 'Jercans ET concentrate',
        'Engine Flush', 'Diesel System Treatment', 'Petrol System Treatment',
        'Authomatic Transmission Treatment', 'Manual Transmission Treatment', 'Octane Boster'
    ],
    'Note Book' => ['note book'],
    'SIMCARD' => ['Simcard'],
    'envelope' => ['envelope'],
    'Remote' => ['remote']
];

foreach ($categories as $category => $subcategories) {
    $insert_category_query = "INSERT INTO categories (name) VALUES (?)";
    $insert_category_stmt = $conn->prepare($insert_category_query);
    $insert_category_stmt->bind_param('s', $category);
    $insert_category_stmt->execute();
    $category_id = $conn->insert_id;

    foreach ($subcategories as $subcategory) {
        $insert_subcategory_query = "INSERT INTO subcategories (name, category_id) VALUES (?, ?)";
        $insert_subcategory_stmt = $conn->prepare($insert_subcategory_query);
        $insert_subcategory_stmt->bind_param('si', $subcategory, $category_id);
        $insert_subcategory_stmt->execute();
    }
}
?>
