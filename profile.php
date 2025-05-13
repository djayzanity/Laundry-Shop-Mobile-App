<?php
session_start();
require 'connection.php'; // Ensure this connects to the database

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user details from the database
$stmt = $pdo->prepare("SELECT full_name, email, contact_number FROM user WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>Profile Page</title>
    <style>
        /* General Reset */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Arial', sans-serif;
        }

        body {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            color: #333; /* Neutral text color */
        }

        /* Container */
        .container {
            width: 400px;
            background-color: #ffffff;
            color: red;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.2);
            text-align: center;
            height: 55vh;
            position: relative;
            overflow: hidden;
        }

        /* Back Button */
        .back-button {
            position: absolute;
            top: 15px;
            left: 15px;
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 14px;
            color: red;
            cursor: pointer;
            text-decoration: none;
        }

        .back-button img {
            width: 20px;
            height: 20px;
        }

        /* Profile Image */
        .profile-image {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid #ff416c; /* Add border */
            margin: 10px auto;
        }

        /* User Information */
        h2 {
            font-size: 28px;
            margin: 15px 0;
        }

        .info {
            display: flex;
            align-items: center;
            gap: 10px;
            justify-content: center;
            font-size: 16px;
            margin: 10px 0;
        }

        .info .icon {
            width: 20px;
            height: 20px;
        }

        /* Separator Line */
        .separator {
            width: 90%;
            height: 1px;
            background-color: red;
            margin: 15px auto;
            opacity: 0.6;
        }

        /* Navigation Links */
        .nav-links {
            margin: 20px 0;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .nav-links a {
            color: white;
            background-color: red;
            text-decoration: none;
            font-size: 14px;
            padding: 10px 15px;
            border-radius: 5px;
            transition: background 0.3s, color 0.3s;
        }

        .nav-links a:hover {
            background-color: #ff2a1a; /* Darker red */
        }

        /* Logout Button */
        .logout-btn {
            margin-top: 20px;
            background-color: red;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            font-size: 16px;
            transition: transform 0.3s ease, background 0.3s ease;
        }

        .logout-btn:hover {
            background-color: #ff2a1a;
            transform: scale(1.05);
        }
    </style>
</head>
<body>
<div class="container">
    <!-- Back Button -->
    <a href="dashboard.php" class="back-button">
        <img src="/back.png" alt="Back Icon"> Back
    </a>

    <!-- User Information -->
    <h2><?php echo htmlspecialchars($user['full_name']); ?></h2>
    <div class="info">
        <img src="/mail.png" alt="Email Icon" class="icon">
        <?php echo htmlspecialchars($user['email']); ?>
    </div>
    <div class="info">
        <img src="/cell.png" alt="Phone Icon" class="icon">
        <?php echo htmlspecialchars($user['contact_number']); ?>
    </div>

    <!-- Separator -->
    <div class="separator"></div>

    <!-- Navigation Links -->
    <div class="nav-links">
        <a href="orderlist.php">My Orders</a>
        <a href="packages.php">Rewards</a>
        <a href="about.php">About Us</a>
    </div>

    <!-- Separator -->
    <div class="separator"></div>

    <!-- Logout Button -->
    <button onclick="logout()" class="logout-btn">LOGOUT</button>
</div>

<script>
    // Logout fade-out effect
    function logout() {
        document.body.classList.add('fade-out');
        setTimeout(() => {
            window.location.href = 'login.php';
        }, 500);
    }
</script>
</body>
</html>
