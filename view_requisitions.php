<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] < 2) {
    header("Location: login.php");
    exit();
}

try {
    $pdo = new PDO('mysql:host=localhost;dbname=stock_management_system', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare("SELECT requisition_date, item_name, status FROM requisitions WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $requisitions = $stmt->fetchAll();
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Requisitions</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <h2 class="text-center mt-5">Your Requisitions</h2>
                <?php if (count($requisitions) > 0): ?>
                    <table class="table table-striped mt-4">
                        <thead>
                            <tr>
                                <th>Requisition Date</th>
                                <th>Item Name</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($requisitions as $requisition): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($requisition['requisition_date']); ?></td>
                                    <td><?php echo htmlspecialchars($requisition['item_name']); ?></td>
                                    <td><?php echo htmlspecialchars($requisition['status']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p class="text-center mt-4">You have no requisitions.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
