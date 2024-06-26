
<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['username']) && isset($_POST['password'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];

        try {
            
            error_reporting(E_ALL);
            ini_set('display_errors', 1);

            
            $pdo = new PDO('mysql:host=localhost;dbname=stock_management_system', 'root', '');
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            
            $stmt = $pdo->prepare("SELECT password_hash FROM users WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch();

            if ($user) {
                if (password_verify($password, $user['password_hash'])) {
                    
                    $_SESSION['username'] = $username;
                    header("Location: admin_home.php"); 
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
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <h2 class="text-center mt-5">Login</h2>
                <form method="POST" action="login.php" class="mt-4">
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
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html






