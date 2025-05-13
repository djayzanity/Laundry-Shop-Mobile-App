<?php
session_start();
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Include PHPMailer classes
require 'PHPMailer-master/src/Exception.php';
require 'PHPMailer-master/src/PHPMailer.php';
require 'PHPMailer-master/src/SMTP.php';
require 'connection.php'; // Database connection

$success_message = "";
$error_message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Get user input
    $email = trim($_POST['email']);
    $gmail_username = trim($_POST['gmail_username']);
    $gmail_password = trim($_POST['gmail_password']); // Gmail app password recommended

    // Validate input
    if (empty($email) || empty($gmail_username) || empty($gmail_password)) {
        $error_message = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Invalid email format.";
    } else {
        try {
            // Check if email exists in the database
            $stmt = $pdo->prepare("SELECT id FROM user WHERE email = :email");
            $stmt->bindParam(':email', $email);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                // Email exists; generate reset token and expiration
                $resetToken = bin2hex(random_bytes(32));
                $expirationTime = (new DateTime())->modify('+1 hour')->format('Y-m-d H:i:s'); // Token valid for 1 hour
                $resetLink = "http://localhost:3000/reset.php" . $resetToken;

                // Update the database with reset token and expiration
                $stmt = $pdo->prepare("UPDATE user SET reset_token = :token, reset_expiration = :expiration WHERE email = :email");
                $stmt->bindParam(':token', $resetToken);
                $stmt->bindParam(':expiration', $expirationTime);
                $stmt->bindParam(':email', $email);
                $stmt->execute();

                // Send reset email using PHPMailer
                $mail = new PHPMailer(true);
                try {
                    $mail->isSMTP();
                    $mail->Host = 'jheussneil7@gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = $gmail_username; // Gmail username
                    $mail->Password = $gmail_password; // Gmail password or App Password
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                    $mail->Port = 465;

                    // Email details
                    $mail->setFrom($gmail_username, 'E&Q Laundry');
                    $mail->addAddress($email);
                    $mail->isHTML(true);
                    $mail->Subject = 'Password Reset Request';
                    $mail->Body = "
                        <p>Hello,</p>
                        <p>You requested a password reset. Click the link below to reset your password:</p>
                        <a href='$resetLink'>$resetLink</a>
                        <p>This link will expire in 1 hour.</p>
                        <p>If you did not request this reset, you can ignore this email.</p>";

                    $mail->send();
                    $success_message = "A password reset link has been sent to your email.";
                } catch (Exception $e) {
                    $error_message = "Failed to send email. Error: {$mail->ErrorInfo}";
                }
            } else {
                $error_message = "Email address not found.";
            }
        } catch (Exception $e) {
            $error_message = "An error occurred. Please try again later.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <style>
        /* Import Google Fonts */
        @import url('https://fonts.googleapis.com/css2?family=Raleway:wght@400;600&display=swap');

        /* General Reset */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Raleway';
        }

        /* Body Styling */
        body {
            background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.7)), 
                        url('/img/laundry.jpeg') no-repeat center center/cover;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            color: white;
        }

        /* Container Styling */
        .container {
            width: 100%;
            max-width: 400px;
            text-align: center;
            }

        /* Header Styling */
        .container h2 {
            margin-bottom: 20px;
            font-size: 24px;
            font-weight: 600;
        }

        /* Input Box Styling */
        .input-box {
            position: relative;
            margin-bottom: 20px;
        }

        .input-box input {
            width: 100%;
            padding: 10px 15px;
            font-size: 16px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background: transparent;
            color: white;
            outline: none;
            transition: border-color 0.3s ease-in-out;
        }

        .input-box input:focus {
            border-color: #ff4d4d;
        }

        .input-box label {
            position: absolute;
            top: 50%;
            left: 15px;
            transform: translateY(-50%);
            font-size: 14px;
            color: #aaa;
            pointer-events: none;
            transition: all 0.3s ease-in-out;
        }

        .input-box input:focus + label,
        .input-box input:not(:placeholder-shown) + label {
            top: -40px;
            left: 10px;
            font-size: 12px;
            color: #ff4d4d;
        }

        /* Button Styling */
        button {
            background-color: #ff4d4d;
            color: white;
            border: none;
            padding: 10px 15px;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
            transition: background-color 0.3s ease-in-out;
        }

        button:hover {
            background-color: #ff6666;
        }

        /* Error and Success Messages */
        .error-message,
        .success-message {
            margin-top: 10px;
            font-size: 14px;
            display: block;
        }

        .error-message {
            color: #e74c3c;
        }

        .success-message {
            color: #2ecc71;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Forgot Password</h2>
        <form method="POST">
            <div class="input-box">
                <input type="email" name="email" required placeholder=" ">
                <label for="email">Email Address</label>
            </div>
            <?php if (!empty($error_message)): ?>
                <div class="error-message"><?php echo htmlspecialchars($error_message); ?></div>
            <?php endif; ?>
            <?php if (!empty($success_message)): ?>
                <div class="success-message"><?php echo htmlspecialchars($success_message); ?></div>
            <?php endif; ?>
            <button type="submit">Submit</button>
        </form>
    </div>
</body>
</html>
