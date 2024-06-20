<?php
session_start();
include('db.php');
include('includes/nav_bar.php'); 

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

function getReport($conn, $interval) {
    $sql = "SELECT items.name, reports.change_type, reports.change_amount, reports.change_date 
            FROM reports 
            JOIN items ON reports.item_id = items.id 
            WHERE reports.change_date >= NOW() - INTERVAL $interval";
    return $conn->query($sql);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['date'])) {
        $date = $_POST['date'];
        $reports = getReport($conn, "'$date 00:00:00'");
    } elseif (isset($_POST['daily'])) {
        $reports = getReport($conn, '1 DAY');
    } elseif (isset($_POST['monthly'])) {
        $reports = getReport($conn, '1 MONTH');
    }
} else {
    $reports = getReport($conn, '1 DAY'); // Default daily report
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Report</title>
</head>
<body>
    <h1>Report</h1>
    
    <form method="post">
        Date: <input type="date" name="date">
        <input type="submit" value="View Report by Date">
    </form>
    
    <form method="post">
        <input type="submit" name="daily" value="View Daily Report">
        <input type="submit" name="monthly" value="View Monthly Report">
    </form>

    <table border="1">
        <tr>
            <th>Item Name</th>
            <th>Change Type</th>
            <th>Change Amount</th>
            <th>Change Date</th>
        </tr>
        <?php while($row = $reports->fetch_assoc()): ?>
        <tr>
            <td><?php echo $row['name']; ?></td>
            <td><?php echo $row['change_type']; ?></td>
            <td><?php echo $row['change_amount']; ?></td>
            <td><?php echo $row['change_date']; ?></td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>
