<?php
 include('includes/nav_bar.php'); 
 

session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}
?>



    <div class="container mt-4">
        <div class="card-deck">
            <!-- Card 1: Items in Stock -->
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Items in Stock</h5>
                    <p class="card-text">Updated when new items come in stock</p>
                    <a href="items.php" class="btn btn-primary">View Items</a>
                </div>
            </div>

            <!-- Card 2: Report -->
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">View Report</h5>
                    <p class="card-text">View detailed reports</p>
                    <a href="report.php" class="btn btn-primary">View Report</a>
                </div>
            </div>
        </div>


    <!-- Bootstrap JS and jQuery (required for Bootstrap functionality) -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
