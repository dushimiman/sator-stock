<?php
session_start();
include(__DIR__ . '/../includes/nav_bar.php');
include(__DIR__ . '/../includes/db.php'); 
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    header("Location: ../login.php"); 
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Stock Management System</title>
    <link rel="icon" href="./images/stock-icon.png" type="image/x-icon"> 
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            padding: 20px;
        }
        .container {
            margin-top: 90px;
            margin-left: 30px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #dddddd;
            text-align: left;
            padding: 8px;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <?php include(__DIR__ . '/../includes/nav_bar.php'); ?>

    <div class="container">
        <h2>All Items</h2>
        <div class="form-group">
            <select id="itemsPerPage" class="form-control w-auto d-inline">
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100">100</option>
            </select>
        </div>
        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead class="thead-dark">
                    <tr>
                        <th>ID</th>
                        <th>Item Name</th>
                        <th>Item Type</th>
                        <th>Serial Number</th>
                        <th>IMEI</th>
                        <th>Quantity</th>
                        <th>Creation Date</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    <?php
                    include(__DIR__ . '/../includes/db.php'); 

                    $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 10; 

                    $query = "SELECT * FROM stock LIMIT ?";
                    $stmt = $mysqli->prepare($query);

                    if ($stmt === false) {
                        die('Prepare failed: ' . htmlspecialchars($mysqli->error));
                    }

                    $bind = $stmt->bind_param("i", $limit);
                    if ($bind === false) {
                        die('Bind param failed: ' . htmlspecialchars($stmt->error));
                    }

                    $execute = $stmt->execute();
                    if ($execute === false) {
                        die('Execute failed: ' . htmlspecialchars($stmt->error));
                    }

                    $result = $stmt->get_result();

                    while ($row = $result->fetch_assoc()) {
                        echo '<tr>';
                        echo '<td>' . htmlspecialchars($row['id']) . '</td>';
                        echo '<td>' . htmlspecialchars($row['item_name']) . '</td>';
                        echo '<td>' . htmlspecialchars($row['item_type']) . '</td>';
                        echo '<td>' . htmlspecialchars($row['serial_number']) . '</td>';
                        echo '<td>' . htmlspecialchars($row['imei']) . '</td>';
                        echo '<td>' . htmlspecialchars($row['quantity']) . '</td>';
                        echo '<td>' . htmlspecialchars($row['creation_date']) . '</td>';
                        echo '</tr>';
                    }

                    $stmt->close();
                    $mysqli->close();
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        function loadTableData(limit) {
            $.ajax({
                url: 'fetch_stock_items.php',
                type: 'GET',
                data: { limit: limit },
                success: function(response) {
                    $('#tableBody').html(response);
                },
                error: function(error) {
                    console.log(error);
                }
            });
        }

        $(document).ready(function() {
            let limit = $('#itemsPerPage').val();
            loadTableData(limit);

            $('#itemsPerPage').change(function() {
                limit = $(this).val();
                loadTableData(limit);
            });
        });
    </script>
</body>
</html>
