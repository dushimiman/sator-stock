<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action']) && $_POST['action'] == 'login') {
        // Login process
        if (isset($_POST['username']) && isset($_POST['password'])) {
            $username = $_POST['username'];
            $password = $_POST['password'];

            try {
             
                $pdo = new PDO('mysql:host=localhost;dbname=stock_management_system', 'root', '');
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

               
                $stmt = $pdo->prepare("SELECT id, username, password_hash, role FROM users WHERE username = ?");
                $stmt->execute([$username]);
                $user = $stmt->fetch();

                if ($user) {
                    
                    if (password_verify($password, $user['password_hash'])) {
                        // Store user session
                        $_SESSION['user_id'] = $user['id'];
                        $_SESSION['username'] = $username;
                        $_SESSION['role'] = $user['role'];

                        
                        if ($user['role'] == 'HQS') {
                            header("Location: admin_home.php");
                        } elseif (in_array($user['role'], ['branch manager', 'rusizi', 'musanze', 'muhanga', 'rwamagana', 'huye'])) {
                            header("Location: branch_manager_home.php");
                        } else {
                            echo "Unknown role.";
                        }
                        exit();
                    } else {
                        echo "Incorrect username or password.";
                    }
                } else {
                    echo "Incorrect username or password.";
                }
            } catch (PDOException $e) {
                echo 'Connection failed: ' . $e->getMessage();
            }
        } else {
            echo "Please enter both username and password.";
        }
    } elseif (isset($_POST['action']) && $_POST['action'] == 'register') {
        // Registration process
        if (isset($_POST['username']) && isset($_POST['password']) && isset($_POST['role'])) {
            $username = $_POST['username'];
            $password = $_POST['password'];
            $role = $_POST['role']; 

            try {
                // Connect to the database
                $pdo = new PDO('mysql:host=localhost;dbname=stock_management_system', 'root', '');
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                // Check if username already exists
                $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM users WHERE username = ?");
                $stmt->execute([$username]);
                $result = $stmt->fetch();

                if ($result['count'] > 0) {
                    echo "Username already exists.";
                } else {
                    // Hash password
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                    // Insert user into database
                    $stmt = $pdo->prepare("INSERT INTO users (username, password_hash, role) VALUES (?, ?, ?)");
                    $stmt->execute([$username, $hashed_password, $role]);

                    echo "User registered successfully.";
                }
            } catch (PDOException $e) {
                echo 'Connection failed: ' . $e->getMessage();
            }
        } else {
            echo "Please fill in all fields.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login and Register</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
              
                <h2 class="text-center mt-3">Register</h2>
                <form method="POST" action="login.php" class="mt-4">
                    <input type="hidden" name="action" value="register">
                    <div class="form-group">
                        <label for="reg_username">Username:</label>
                        <input type="text" class="form-control" id="reg_username" name="username" required>
                    </div>
                    <div class="form-group">
                        <label for="reg_password">Password:</label>
                        <input type="password" class="form-control" id="reg_password" name="password" required>
                    </div>
                    <div class="form-group">
                        <label for="role">Role:</label>
                        <select class="form-control" id="role" name="role" required>
                            <option value="HQS">HQS</option>
                            <option value="branch manager">Branch Manager</option>
                            <option value="rusizi">Rusizi</option>
                            <option value="musanze">Musanze</option>
                            <option value="muhanga">Muhanga</option>
                            <option value="rwamagana">Rwamagana</option>
                            <option value="huye">Huye</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-success btn-block">Register</button>
                </form>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
