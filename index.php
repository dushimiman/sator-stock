<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generate Report</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">Generate Report</h2>
        <form method="POST" action="report_result.php">
        <div class="form-group col-md-4">
                    <label for="report_type">Report Type:</label>
                    <select id="report_type" name="report_type" class="form-control">
                        <option value="daily">Daily</option>
                        <option value="monthly">Monthly</option>
                        <option value="custom">Custom Date Range</option>
                    </select>
                </div>
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="start_date">Start Date:</label>
                    <input type="date" id="start_date" name="start_date" class="form-control" required>
                </div>
                <div class="form-group col-md-4">
                    <label for="end_date">End Date:</label>
                    <input type="date" id="end_date" name="end_date" class="form-control" required>
                </div>
                
            </div>
            <button type="submit" class="btn btn-primary">Generate Report</button>
        </form>
        <form method="POST" action="generate_pdf.php">
  
   
</form>
        <div id="reportResult" class="mt-5">
            <?php include 'report_result.php'; ?>
        </div>
    </div>
</body>
</html>
