<?php
// Start the session
session_start();
include '../connection.php';

// Default username and hashed password
$defaultUsername = 'enqlaundry';
$defaultPassword = 'enqlaundry';
$hashedPassword = password_hash($defaultPassword, PASSWORD_DEFAULT);

// Initialize the $loginError variable
$loginError = ""; 

// Check for logout
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
    // Destroy the session and redirect to login page
    session_destroy();
    header('Location: adminlogin.php');
    exit;
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $inputUsername = $_POST['username'] ?? ''; // Safeguard for undefined keys
    $inputPassword = $_POST['password'] ?? ''; // Safeguard for undefined keys

    // Validate login with hardcoded values
    if ($inputUsername === $defaultUsername && password_verify($inputPassword, $hashedPassword)) {
        // Login is successful, create a session for the user
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin'] = $inputUsername;

        // Redirect to the adminaccept.php page
        header('Location: adminaccept.php');
        exit;
    } else {
        $loginError = "Invalid username or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="stylesheet" href="/admin_css/admin_login.css">
</head>
<body>
    <div class="login-container">
        <div class="logo">
            <img src="/img/logo new.png" alt="E&Q Laundry">
        </div>

        <h3>ADMIN LOGIN</h3>

        <?php if (isset($loginError)): ?>
            <p style="color: red;"><?php echo $loginError; ?></p>
        <?php endif; ?>

        <form action="" method="post">
            <div class="input-container">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" placeholder="Enter username" required>
            </div>

            <div class="input-container">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter password" required>
            </div>

            <button type="submit">Login</button>
        </form>
    </div>

    <script src="script.js"></script>
</body>
</html>
