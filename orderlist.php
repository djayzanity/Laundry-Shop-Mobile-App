<?php
session_start();
require 'connection.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Clear history if the clear history button is clicked
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['clear_history'])) {
    $clearStmt = $pdo->prepare("DELETE FROM _statement WHERE user_id = ?");
    $clearStmt->execute([$user_id]);
    header('Location: orderlist.php'); // Reload the page after clearing
    exit();
}

// Fetch orders from _statement table for the logged-in user
$stmt = $pdo->prepare("SELECT _transaction FROM _statement WHERE user_id = ?");
$stmt->execute([$user_id]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .order-details-container {
            width: 90%;
            max-width: 420px;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            overflow-y: auto;
            max-height: 90vh;
            margin: auto;
        }

        h2 {
            text-align: center;
            color: #D32F2F;
            margin-bottom: 20px;
        }

        .order-info, .order-summary {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 8px;
            background-color: #f9f9f9;
        }

        .order-info div, .order-summary div {
            margin: 10px 0;
        }

        .back-button, .clear-button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #D32F2F;
            color: white;
            font-weight: bold;
            text-transform: uppercase;
            border-radius: 5px;
            text-decoration: none;
            text-align: center;
            width: 91%;
            margin-bottom: 10px;
        }

        .clear-button {
            background-color: #FF0000;
            margin-top: 10px;
            display: inline-block;
            padding: 10px 20px;
            color: white;
            font-weight: bold;
            text-transform: uppercase;
            border-radius: 5px;
            text-decoration: none;
            text-align: center;
            width: 91%;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>

<div class="order-details-container">
    <h2>Order Details</h2>

    <?php if ($orders): ?>
        <?php foreach ($orders as $index => $order): ?>
            <?php
            // Decode the _transaction JSON data
            $transactionData = json_decode($order['_transaction'], true);
            
            if ($transactionData) {
                $orderNumber = $index + 1;
                $status = $transactionData['status'] ?? 'Pending';
                $date = !empty($transactionData['order_date']) ? date('j F, Y', strtotime($transactionData['order_date'])) : 'N/A';
                $price = $transactionData['total_amount'] ?? 0.00;
                $packageName = $transactionData['package_name'] ?? 'N/A';
                $totalServiceMinutes = $transactionData['total_minutes'] ?? 'N/A';

                $includedServices = $transactionData['included_services'] ?? [];
            } else {
                echo "Error decoding order details.";
                continue;
            }
            ?>

            <div class="order-info">
                <div><strong>Order Number:</strong> <?= htmlspecialchars($orderNumber) ?></div>
                <div><strong>Status:</strong> <?= htmlspecialchars($status) ?></div>
                <div><strong>Date:</strong> <?= htmlspecialchars($date) ?></div>
                <div><strong>Package Name:</strong> <?= htmlspecialchars($packageName) ?></div>
                <div><strong>Approximate Time:</strong> <?= htmlspecialchars($totalServiceMinutes) ?> minutes</div>
                
                <!-- Included Services under Package Name -->
                <div><strong>Included Services:</strong></div>
                <ul>
                    <?php foreach ($includedServices as $service): ?>
                        <li><?= htmlspecialchars($service); ?></li>
                    <?php endforeach; ?>
                </ul>
                
                <div><strong>Total Price:</strong> ₱<?= htmlspecialchars(number_format($price, 2)) ?></div>
            </div>

        <?php endforeach; ?>
    <?php else: ?>
        <p>No orders found</p>
    <?php endif; ?>

    <a href="userdashboard.php" class="back-button">Back to Dashboard</a>
    
    <!-- Clear History Form -->
    <form method="POST" style="text-align: center;">
        <button type="submit" name="clear_history" class="clear-button">Clear History</button>
    </form>
</div>

</body>
</html>
