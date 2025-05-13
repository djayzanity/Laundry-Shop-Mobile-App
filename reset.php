<?php
session_start();
require 'connection.php'; // Include database connection
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Include PHPMailer classes
require 'PHPMailer-master/src/Exception.php';
require 'PHPMailer-master/src/PHPMailer.php';
require 'PHPMailer-master/src/SMTP.php';

$success_message = "";
$error_message = "";

// Check if the token is provided
if (isset($_GET['token'])) {
    $resetToken = $_GET['token'];

    // Validate the token
    $stmt = $pdo->prepare("SELECT id, email, reset_token, reset_expiration FROM user WHERE reset_token = :resetToken");
    $stmt->bindParam(':resetToken', $resetToken);
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        $user = $stmt->fetch();
        
        // Check if the token has expired
        if (new DateTime() > new DateTime($user['reset_expiration'])) {
            $error_message = "The reset link has expired.";
        } else {
            // Token is valid, show password reset form
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $newPassword = trim($_POST['new_password']);
                $confirmPassword = trim($_POST['confirm_password']);
                
                // Validate the passwords
                if (empty($newPassword) || empty($confirmPassword)) {
                    $error_message = "Please fill in both password fields.";
                } elseif ($newPassword !== $confirmPassword) {
                    $error_message = "Passwords do not match.";
                } else {
                    // Hash the new password
                    $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
                    
                    // Update the password in the database
                    $stmt = $pdo->prepare("UPDATE user SET pass = :password, reset_token = NULL, reset_expiration = NULL WHERE id = :id");
                    $stmt->bindParam(':password', $hashedPassword);
                    $stmt->bindParam(':id', $user['id']);
                    $stmt->execute();

                    // Send a confirmation email
                    try {
                        $mail = new PHPMailer(true);
                        $mail->isSMTP();
                        $mail->Host = 'smtp.gmail.com'; // SMTP server
                        $mail->SMTPAuth = true;
                        $mail->Username = 'jheussneil7@gmail.com'; // Your email
                        $mail->Password = 'vgequjxwaefwbnoz'; // Your email app password
                        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                        $mail->Port = 465;

                        $mail->setFrom('jheussneil7@gmail.com', 'http://ec2-3-25-112-67.ap-southeast-2.compute.amazonaws.com/login.php');
                        $mail->addAddress($user['email']); // User's email

                        $mail->isHTML(true);
                        $mail->Subject = 'Password Reset Successful';
                        $mail->Body = "<p>Your password has been reset successfully. If you did not request this change, please contact our support team immediately.</p>";

                        $mail->send();
                        $success_message = "Your password has been reset successfully. A confirmation email has been sent.";
                    } catch (Exception $e) {
                        $error_message = "Your password was reset, but we couldn't send a confirmation email. Mailer Error: {$mail->ErrorInfo}";
                    }
                }
            }
        }
    } else {
        $error_message = "Invalid reset token.";
    }
} else {
    $error_message = "No reset token provided.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <style>
        /* Styling matches the design you provided */
        @import url('https://fonts.googleapis.com/css2?family=Raleway:wght@400;600&display=swap');

        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Raleway', sans-serif; }
        body {
            background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.7)), 
                        url('/img/background.jpg') no-repeat center center/cover;
            min-height: 100vh; display: flex; justify-content: center; align-items: center; color: white;
        }
        .container {
            background-color: rgba(0, 0, 0, 0.8); padding: 30px 20px; border-radius: 10px;
            width: 100%; max-width: 400px; text-align: center; box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.3);
        }
        .container h2 { margin-bottom: 20px; font-size: 24px; font-weight: 600; }
        .input-box { position: relative; margin-bottom: 20px; }
        .input-box input {
            width: 100%; padding: 10px 15px; font-size: 16px; border: 1px solid #ddd;
            border-radius: 5px; background: transparent; color: white; outline: none;
            transition: border-color 0.3s ease-in-out;
        }
        .input-box input:focus { border-color: #ff4d4d; }
        .input-box label {
            position: absolute; top: 50%; left: 15px; transform: translateY(-50%);
            font-size: 14px; color: #aaa; pointer-events: none; transition: all 0.3s ease-in-out;
        }
        .input-box input:focus + label, .input-box input:not(:placeholder-shown) + label {
            top: 5px; left: 10px; font-size: 12px; color: #ff4d4d;
        }
        button {
            background-color: #ff4d4d; color: white; border: none; padding: 10px 15px;
            font-size: 16px; border-radius: 5px; cursor: pointer; width: 100%;
            transition: background-color 0.3s ease-in-out;
        }
        button:hover { background-color: #ff6666; }
        .error-message, .success-message {
            margin-top: 10px; font-size: 14px; color: #e74c3c; display: block;
        }
        .success-message { color: #2ecc71; }
        @media (max-width: 480px) {
            .container { padding: 20px 10px; }
            .container h2 { font-size: 20px; }
            button { font-size: 14px; padding: 8px 10px; }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Reset Your Password</h2>
        
        <!-- Display error or success messages -->
        <?php if (!empty($error_message)): ?>
            <div class="error-message"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>
        <?php if (!empty($success_message)): ?>
            <div class="success-message"><?php echo htmlspecialchars($success_message); ?></div>
        <?php endif; ?>

        <!-- Password reset form -->
        <form action="reset.php?token=<?php echo $resetToken; ?>" method="POST">
            <div class="input-box">
                <input type="password" name="new_password" id="new_password" required placeholder="New Password">
                <label for="new_password">New Password</label>
            </div>
            <div class="input-box">
                <input type="password" name="confirm_password" id="confirm_password" required placeholder="Confirm Password">
                <label for="confirm_password">Confirm Password</label>
            </div>
            <button type="submit">Reset Password</button>
        </form>
    </div>
</body>
</html>
