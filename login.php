<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action']) && $_POST['action'] == 'login') {
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
                        $_SESSION['user_id'] = $user['id'];
                        $_SESSION['username'] = $username;
                        $_SESSION['role'] = $user['role'];

                        if ($user['id'] == 1) {
                            header("Location: admin_home.php");
                        } elseif ($user['id'] == 2) {
                            header("Location: user_home.php");
                        } elseif ($user['id'] >= 2) {
                            header("Location: user_home.php");  
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
        if (isset($_POST['username']) && isset($_POST['password']) && isset($_POST['role'])) {
            $username = $_POST['username'];
            $password = $_POST['password'];
            $role = $_POST['role'];

            try {
                $pdo = new PDO('mysql:host=localhost;dbname=stock_management_system', 'root', '');
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM users WHERE username = ?");
                $stmt->execute([$username]);
                $result = $stmt->fetch();

                if ($result['count'] > 0) {
                    echo "Username already exists.";
                } else {
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

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
                <h2 class="text-center mt-5">Login</h2>
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
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
