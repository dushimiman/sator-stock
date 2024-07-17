<!DOCTYPE html>
<html>
<head>
    <title>Stock Management System</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        /* Custom CSS styles */
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
    <!-- Include the navbar -->
    <?php include('includes/nav_bar.php'); ?>

    <div class="container">
        <h2>Stock Items</h2>
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
                <tbody>
                    <?php
                    
                    try {
                        $pdo = new PDO('mysql:host=localhost;dbname=stock_management_system', 'root', '');
                        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                        // Fetching data from stock table
                        $stmt = $pdo->query('SELECT * FROM stock');
                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            echo '<tr>';
                            echo '<td>'.$row['id'].'</td>';
                            echo '<td>'.$row['item_name'].'</td>';
                            echo '<td>'.$row['item_type'].'</td>';
                            echo '<td>'.$row['serial_number'].'</td>';
                            echo '<td>'.$row['imei'].'</td>';
                            echo '<td>'.$row['quantity'].'</td>';
                            echo '<td>'.$row['creation_date'].'</td>';
                            echo '</tr>';
                        }
                    } catch (PDOException $e) {
                        echo 'Error: ' . $e->getMessage();
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Bootstrap and other scripts -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
