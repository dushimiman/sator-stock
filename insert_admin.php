<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "sock_management_system";


$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_POST['username']) && isset($_POST['password']) && isset($_POST['role'])) {
   
    $new_username = $_POST['username'];
    $new_password = $_POST['password'];
    $role = $_POST['role'];

    // Hash the password
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

    // Insert user into the database
    $sql = "INSERT INTO users (username, password_hash, role) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    
    // Check if the statement was prepared correctly
    if ($stmt) {
        $stmt->bind_param("sss", $new_username, $hashed_password, $role);

        if ($stmt->execute()) {
            echo "User registered successfully.";
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "Error preparing statement: " . $conn->error;
    }
} else {
    echo "All form fields are required.";
}

$conn->close();
?>


<form action="insert_admin.php" method="post">
    <label for="username">Username:</label>
    <input type="text" id="username" name="username" required><br>
    <label for="password">Password:</label>
    <input type="password" id="password" name="password" required><br>
    <label for="role">Role:</label>
    <select id="role" name="role" required>
        <option value="stock_manager">Stock Manager</option>
        <option value="other">Other</option>
    </select><br>
    <input type="submit" value="Register">
</form>

