<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "sock_management_system";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$name = "";
$serial_number = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $serial_number = $_POST['serial_number'];

    
    $insert_query = "INSERT INTO items (name, serial_number) VALUES (?, ?)";
    $insert_stmt = $conn->prepare($insert_query);

   
    if ($insert_stmt) {
       
        $insert_stmt->bind_param('ss', $name, $serial_number);

        if ($insert_stmt->execute()) {
            
            header('Location: items.php');
            exit(); 
        } else {
           
            echo "Error: " . $insert_stmt->error;
        }
    } else {
   
        echo "Error preparing statement: " . $conn->error;
    }
}
?>
<form method="POST">
    Name: <input type="text" name="name" value="<?php echo htmlspecialchars($name); ?>" required><br>
    Serial Number: <input type="text" name="serial_number" value="<?php echo htmlspecialchars($serial_number); ?>" required><br>
    <button type="submit">Add Item</button>
</form>
