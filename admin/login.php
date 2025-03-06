<?php
// admin-login.php
session_start();
require_once '../includes/config.php';

// Check if already logged in
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: admin/dashboard.php');
    exit;
}

$error = '';

// Process login form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $error = 'Please enter both username and password';
    } else {
        // Fetch admin user from database
        $stmt = $conn->prepare("SELECT id, username, password FROM admin_users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            // Verify password
            if (password_verify($password, $user['password'])) {
                // Set session variables
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_id'] = $user['id'];
                $_SESSION['admin_username'] = $user['username'];
                
                // Log successful login
                $logStmt = $conn->prepare("INSERT INTO admin_login_logs (admin_id, ip_address, user_agent, status) VALUES (?, ?, ?, 'success')");
                $ip = $_SERVER['REMOTE_ADDR'];
                $userAgent = $_SERVER['HTTP_USER_AGENT'];
                $logStmt->bind_param("iss", $user['id'], $ip, $userAgent);
                $logStmt->execute();
                
                // Redirect to dashboard
                header('Location: admin/dashboard.php');
                exit;
            } else {
                $error = 'Invalid password';
                
                // Log failed login attempt
                $logStmt = $conn->prepare("INSERT INTO admin_login_logs (username, ip_address, user_agent, status) VALUES (?, ?, ?, 'failed')");
                $ip = $_SERVER['REMOTE_ADDR'];
                $userAgent = $_SERVER['HTTP_USER_AGENT'];
                $logStmt->bind_param("sss", $username, $ip, $userAgent);
                $logStmt->execute();
            }
        } else {
            $error = 'User not found';
            
            // Log invalid username attempt
            $logStmt = $conn->prepare("INSERT INTO admin_login_logs (username, ip_address, user_agent, status) VALUES (?, ?, ?, 'invalid_user')");
            $ip = $_SERVER['REMOTE_ADDR'];
            $userAgent = $_SERVER['HTTP_USER_AGENT'];
            $logStmt->bind_param("sss", $username, $ip, $userAgent);
            $logStmt->execute();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body class="admin-login-page">
    <div class="admin-login-container">
        <div class="admin-login-box">
            <div class="admin-login-logo">
                <img src="../assets/images/logo.png" alt="Logo">
            </div>
            <h2>Admin Panel</h2>
            
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <form method="POST" action="" class="admin-login-form">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>
                
                <button type="submit" class="btn btn-primary btn-block">Login</button>
            </form>
        </div>
    </div>
    
    <script src="assets/js/admin.js"></script>
</body>
</html>