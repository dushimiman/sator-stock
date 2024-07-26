<?php
session_start();
include('includes/db.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action']) && $_POST['action'] == 'login') {
        if (isset($_POST['username']) && isset($_POST['password'])) {
            $username = $_POST['username'];
            $password = $_POST['password'];

            $stmt = $mysqli->prepare("SELECT id, username, password_hash, role FROM users WHERE username = ?");
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();

            if ($user && password_verify($password, $user['password_hash'])) {
                // Set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $username;
                $_SESSION['role'] = $user['role'];

                // Redirect based on role
                switch ($user['role']) {
                    case 'admin':
                        header("Location: dashboard/admin_home.php");
                        break;
                    case 'user':
                        header("Location: dashboard/user_home.php");
                        break;
                    case 'requester':
                        header("Location: requests/request_list.php");
                        break;
                    default:
                        echo "Unknown role.";
                        break;
                }
                exit();
            } else {
                echo "Incorrect username or password.";
            }
        } else {
            echo "Please enter both username and password.";
        }
    } elseif (isset($_POST['action']) && $_POST['action'] == 'register') {
        if (isset($_POST['username']) && isset($_POST['password']) && isset($_POST['role'])) {
            $username = $_POST['username'];
            $password = $_POST['password'];
            $role = $_POST['role'];

            $stmt = $mysqli->prepare("SELECT COUNT(*) as count FROM users WHERE username = ?");
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();
            $count = $result->fetch_assoc()['count'];

            if ($count > 0) {
                echo "Username already exists.";
            } else {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                $stmt = $mysqli->prepare("INSERT INTO users (username, password_hash, role) VALUES (?, ?, ?)");
                $stmt->bind_param("sss", $username, $hashed_password, $role);
                $stmt->execute();

                echo "User registered successfully.";
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
    <title>Stock Management System</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="icon" href="../images/stock-icon.png" type="image/x-icon">
    <style>
        .login-container {
            max-width: 400px;
        }
        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
        }
        .logo {
            text-align: center;
            margin-bottom: 20px;
        }
        .logo img {
            max-width: 150px;
            margin-right:70em;
        }
        .stock-icon {
            width: 30px;
            height: 30px;
            margin-right: 10px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="logo">
            <img src="./images/Capture.PNG" alt="Company Logo" class="img-fluid">
        </div>
        <div class="row justify-content-center mt-5">
            <div class="col-md-6 login-container">
                <h2 class="text-center">Login</h2>
                <form method="POST" action="login.php" class="mt-4">
                    <input type="hidden" name="action" value="login">
                    <div class="form-group">
                        <label for="username">Username:</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Password:</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">Login</button>
                </form>
                <hr>
            </div>
        </div>
    </div>
    <footer class="footer mt-auto py-3 bg-light">
        <div class="container text-center">
            <span class="text-muted">&copy; 2024 SATOR RWANDA Ltd. All rights reserved.</span>
        </div>
    </footer>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
