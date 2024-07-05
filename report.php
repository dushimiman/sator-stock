<?php 
include('includes/nav_bar.php');?> 
 
 <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Stock Management System</title>
    
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap-theme.min.css">

    <style>
       
        .card {
            margin-bottom: 20px; 
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        

        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Generate Daily Report</h5>
                        <form action="daily_report.php" method="post">
                            <button type="submit" class="btn btn-primary btn-block">Generate Report</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Generate Report by Date</h5>
                        <form action="date_report.php" method="post">
                            <div class="form-group">
                                <label for="date">Select Date:</label>
                                <input type="date" id="date" name="date" class="form-control">
                            </div>
                            <button type="submit" class="btn btn-primary btn-block">Generate Report</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Generate Monthly Report</h5>
                        <form action="monthly_report.php" method="post">
                            <button type="submit" class="btn btn-primary btn-block">Generate Report</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS (optional for some features like dropdowns) -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>
