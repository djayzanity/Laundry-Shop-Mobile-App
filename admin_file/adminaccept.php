<?php
session_start();
require '../connection.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: adminlogin.php');
    exit();
}

// Fetch orders from _statement table
$stmt = $pdo->prepare("SELECT id, user_id, promo_id, _transaction, created_at, started_at, ended_at, delivered_at FROM _statement WHERE delivered_at IS NULL");
$stmt->execute();
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle AJAX request for updating order status
if (isset($_POST['update_status']) && isset($_POST['order_id'])) {
    $order_id = $_POST['order_id'];
    $status_type = $_POST['status_type'];

    if ($status_type === 'decline') {
        // Delete the order from the database
        $stmt = $pdo->prepare("DELETE FROM _statement WHERE id = ?");
        $stmt->execute([$order_id]);
        
        echo json_encode(['status' => 'success', 'message' => 'Order declined and removed successfully']);
        exit();
    }

    $update_field = '';
    $timestamp_value = date('Y-m-d H:i:s');
    
    if ($status_type === 'accept') {
        $status_message = 'Your order has been accepted.';
        $update_field = 'created_at';
        $newStatus = "Accepted";
        
        // Fetch order details including user and promo information
        $stmt = $pdo->prepare("SELECT user_id, promo_id, _transaction FROM _statement WHERE id = ?");
        $stmt->execute([$order_id]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);
        $user_id = $order['user_id'];
        $promo_id = $order['promo_id'];
        
        // Fetch user and promo points if promo_id is valid
        if ($promo_id) {
            // Get promo points_reward and points_cost
            $promoStmt = $pdo->prepare("SELECT points_reward, points_cost FROM promo WHERE id = ?");
            $promoStmt->execute([$promo_id]);
            $promo = $promoStmt->fetch(PDO::FETCH_ASSOC);
            
            if ($promo) {
                $points_reward = (int)$promo['points_reward'];
                $points_cost = (int)$promo['points_cost'];
                
                // Get total order price from transaction data
                $transactionData = json_decode($order['_transaction'], true);
                $price = isset($transactionData['total_amount']) ? (float)$transactionData['total_amount'] : 0.00;

                // Update user points based on promo details
                if ($points_reward > 0) {
                    // Add reward points to user
                    $pdo->prepare("UPDATE user SET points = points + ? WHERE id = ?")
                        ->execute([$points_reward, $user_id]);
                }
                
                if ($points_cost > 0 && $price == 0) {
                    // Deduct cost points from user if the price is zero
                    $pdo->prepare("UPDATE user SET points = GREATEST(points - ?, 0) WHERE id = ?")
                        ->execute([$points_cost, $user_id]);
                }
            }
        }
    } elseif ($status_type === 'start') {
        $status_message = 'Your order has been started.';
        $update_field = 'started_at';
        $newStatus = "Started";
    } elseif ($status_type === 'end') {
        $status_message = 'Your order is ready for delivery.';
        $update_field = 'ended_at';
        $newStatus = 'Ended';
    } elseif ($status_type === 'deliver') {
        $status_message = 'Your order has been delivered.';
        $update_field = 'delivered_at';
        $newStatus = "Delivered";
    }

    // Update the `_transaction` status in JSON format
    $stmt = $pdo->prepare("UPDATE _statement SET $update_field = ?, _transaction = JSON_SET(_transaction, '$.status', ?) WHERE id = ?");
    $stmt->execute([$timestamp_value, $newStatus, $order_id]);

    echo json_encode(['status' => 'success', 'message' => 'Status updated successfully']);
    exit();
}
?>





<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Accepting Orders</title>
   <!-- Include Font Awesome CDN -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <style>
    /* Mobile responsive base styles */
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background-color: #f5f5f5;
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    /* Container for centering content */
    .container {
      max-width: 1000px;
      margin: 0 auto;
      padding: 20px;
    }

    .tabs {
      display: flex;
      flex-direction: row;
      justify-content: space-between;
      background-color: #ffffff;
      border-bottom: 2px solid #e63946;
      margin-bottom: 20px;
      padding: 10px;
    }

    .tab {
      padding: 10px 20px;
      font-size: 1em;
      cursor: pointer;
      transition: background-color 0.3s ease;
      color: #333;
      text-align: center;
      flex: 1;
      text-transform: uppercase;
      font-weight: bold;
    }

    .tab:hover {
      background-color: #e63946;
      color: white;
    }

    .active-tab {
      background-color: #e63946;
      color: white;
    }

    .order-card {
      max-width: 100%;
      margin: 10px auto;
      padding: 20px;
      background: white;
      border-radius: 15px;
      box-shadow: 0 12px 30px rgba(0, 0, 0, 0.1);
      transition: transform 0.3s ease, box-shadow 0.3s ease;
      animation: fadeIn 1s ease-out;
      margin-bottom: 20px;
    }

 
    h2 {
      font-size: 1.8em;
      color: #e63946;
      margin-bottom: 10px;
      text-align: center;
      font-weight: bold;
    }

    .price {
      text-align: right;
      font-size: 1.3em;
      color: #e63946;
      margin-top: 5px;
      font-weight: bold;
    }

    .order-details {
      display: flex;
      justify-content: space-between;
      margin-top: 20px;
      font-size: 1em;
      color: #333;
      flex-direction: column;
    }

    .order-details p {
      margin: 5px 0;
    }

    .order-details span {
      font-weight: bold;
      color: #e63946;
    }

    .order-status {
      display: inline-block;
      padding: 8px 15px;
      margin-top: 15px;
      background-color: #e63946;
      color: white;
      font-weight: bold;
      border-radius: 30px;
      text-align: center;
      text-transform: uppercase;
      letter-spacing: 1px;
      transition: background-color 0.3s ease, transform 0.2s ease;
    }

  /* Update Status Buttons Container */
.order-status-buttons {
  display: flex;
  justify-content: space-between;
  gap: 5px;
  margin-top: 10px;
  flex-wrap: wrap; /* Allows buttons to wrap on smaller screens */
}

/* Common Button Styles */
.status-btn {
  padding: 12px 20px;
  font-size: 12px;
  font-weight: bold;
  border: none;
  border-radius: 8px;
  background-color: red;
  color: white;
  cursor: pointer;
  transition: background-color 0.3s ease, transform 0.3s ease;
  flex-grow: 1; /* Make buttons expand equally */
  text-transform: uppercase;
  text-align: center;
  width: 70%; /* Ensure buttons are full width on smaller screens */
  max-width: 150px; /* Limit width on larger screens */
  box-sizing: border-box;
  margin-bottom: 10px;
}


/* Active button */
.status-btn.active {
  background-color: #2a9d8f;
}

/* Decline button */
.decline-btn {
  background-color:red; /* Light background for decline */
  color: white; /* Text color for decline */
}

.decline-btn:hover {
  background-color: #a8dadc; /* Hover effect for decline button */
  color: white;
  transform: translateY(-2px); /* Slight lift on hover */
}

    .address {
      margin-top: 20px;
      font-size: 1em;
      color: #555;
      line-height: 1.5;
      text-align: center;
      font-style: italic;
    }

    .order-header {
      background-color: #e63946;
      color: white;
      padding: 15px;
      text-align: center;
      border-radius: 10px 10px 0 0;
      margin-bottom: 20px;
    }

     
/* Logout Icon Button */
.logout-icon-btn {
  background: none; /* No background color */
  border: none; /* Remove border */
  padding: 10px;
  cursor: pointer; /* Show pointer on hover */
  transition: transform 0.3s ease, background-color 0.3s ease; /* Smooth transition for hover effects */
  display: flex;
  justify-content: center;
  align-items: center;
}

/* Hover effect */
.logout-icon-btn:hover {
  transform: scale(1.1); /* Slightly enlarge the icon */
  background-color: rgba(255, 255, 255, 0.1); /* Optional light background effect */
  border-radius: 50%; /* Optional circular background */
}

/* Font Awesome Icon */
.logout-icon-btn i {
  font-size: 30px; /* Icon size */
  color: #e63946; /* Icon color */
  transition: color 0.3s ease; /* Transition effect for icon color */
}

    /* Media Queries for Mobile */
    @media (max-width: 768px) {
      .tabs {
        flex-direction: column;
      }

      .tab {
        padding: 12px;
        font-size: 1.1em;
        margin-bottom: 10px;
        width: 93%;
      }

      .order-card {
        padding: 15px;
      }

      h2 {
        font-size: 1.6em;
      }

      .price {
        font-size: 1.4em;
      }

      .order-details {
        flex-direction: column;
      }

      .order-status {
        width: 100%;
        text-align: center;
      }

      .address {
        font-size: 0.95em;
      }
    }

    /* For very small screens */
    @media (max-width: 480px) {
      .order-card {
        padding: 10px;
      }

      h2 {
        font-size: 1.4em;
      }

      .price {
        font-size: 1.2em;
      }

      .order-details p {
        font-size: 0.95em;
      }

      .order-status {
        padding: 8px 15px;
        font-size: 1em;
      }

      .address {
        font-size: 0.85em;
      }
    }

  </style>
</head>
<body>

  <div class="container">
    <div class="tabs">
      <div class="tab active-tab" onclick="showOrders('new')">New Orders</div>
    <!-- Logout Icon at the Bottom -->
    <div class="footer">
  <form action="adminlogin.php" method="POST">
    <input type="hidden" name="logout" value="true">
    <button type="submit" class="logout-icon-btn">
      <i class="fas fa-sign-out-alt"></i>
    </button>
  </form>
</div>

    </div>

    <div id="new-orders" class="order-container">
    <?php foreach ($orders as $order): ?>
        <?php
            $transactionData = json_decode($order['_transaction'], true);
            $orderNumber = $order['id'];
            $status = 'Pending';
            if (!empty($order['delivered_at'])) {
                $status = 'Delivered';
            } elseif (!empty($order['ended_at'])) {
                $status = 'Ended';
            } elseif (!empty($order['started_at'])) {
                $status = 'Started';
            } elseif (!empty($order['created_at'])) {
                $status = 'Accepted';
            }
            $price = isset($transactionData['total_amount']) ? (float) $transactionData['total_amount'] : 0.00;
            $date = !empty($transactionData['order_date']) ? date('j F, Y', strtotime($transactionData['order_date'])) : 'N/A';

            // Fetch user details
            $userStmt = $pdo->prepare("SELECT full_name, contact_number, _address FROM user WHERE id = ?");
            $userStmt->execute([$order['user_id']]);
            $userDetails = $userStmt->fetch(PDO::FETCH_ASSOC);
        ?>

        <div class="order-card p-4 bg-white shadow-md rounded-md flex flex-col mb-4">
            <div class="order-header flex justify-between items-center mb-2">
                <h3 class="text-md font-bold">Order No: <?= htmlspecialchars($orderNumber); ?></h3>
                <p class="text-blue-500"><?= htmlspecialchars($status); ?></p>
            </div>

            <!-- User Details -->
            <?php if ($userDetails): ?>
                <div class="user-details mb-3">
                    <p><strong>User Name:</strong> <?= htmlspecialchars($userDetails['full_name']); ?></p>
                    <p><strong>Contact:</strong> <?= htmlspecialchars($userDetails['contact_number']); ?></p>
                    <p><strong>Address:</strong> <?= htmlspecialchars($userDetails['_address']); ?></p>
                </div>
            <?php endif; ?>

            <!-- Order Details -->
            <div class="order-details mb-3">
                <p><strong>Price:</strong> ₱<?= htmlspecialchars(number_format($price, 2)); ?></p>
                <p><strong>Order Date:</strong> <?= htmlspecialchars($date); ?></p>

                <!-- Display ordered items if available in transaction data -->
                <?php if (isset($transactionData['items']) && is_array($transactionData['items'])): ?>
                    <div class="order-items">
                        <h4 class="font-semibold">Items Ordered:</h4>
                        <ul>
                            <?php foreach ($transactionData['items'] as $item): ?>
                                <li>
                                    <?= htmlspecialchars($item['name']); ?> - Quantity: <?= htmlspecialchars($item['quantity']); ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Update Status Buttons -->
            <div class="order-status-buttons mt-3">
                <button class="status-btn" onclick="updateStatus(<?= $orderNumber; ?>, 'accept')">Accept</button>
                <button class="status-btn" onclick="updateStatus(<?= $orderNumber; ?>, 'start')">Start</button>
                <button class="status-btn" onclick="updateStatus(<?= $orderNumber; ?>, 'end')">End</button>
                <button class="status-btn" onclick="updateStatus(<?= $orderNumber; ?>, 'deliver')">Deliver</button>
                <button class="status-btn decline-btn" onclick="updateStatus(<?= $orderNumber; ?>, 'decline')">Decline</button>
            </div>

   

        </div>
    <?php endforeach; ?>
</div>
  </div>


  <script>
     // Function to handle the update of order status
     function updateStatus(orderId, statusType) {
        let confirmationMessage = '';

        if (statusType === 'accept') {
            confirmationMessage = 'Are you sure you want to accept this order?';
        } else if (statusType === 'start') {
            confirmationMessage = 'Are you sure you want to start this order?';
        } else if (statusType === 'end') {
            confirmationMessage = 'Are you sure you want to mark this order as completed?';
        } else if (statusType === 'deliver') {
            confirmationMessage = 'Are you sure you want to deliver this order?';
        } else if (statusType === 'decline') {
            confirmationMessage = 'Are you sure you want to decline and remove this order?';
        }

        if (confirm(confirmationMessage)) {
            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'adminaccept.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    const response = JSON.parse(xhr.responseText);
                    alert(response.message);
                    location.reload(); // Reload to update the order list
                }
            };
            xhr.send('update_status=1&order_id=' + orderId + '&status_type=' + statusType);
        }
    }

    function showOrders(tab) {
      // Hide all tabs
      document.getElementById('new-orders').style.display = 'none';
      document.getElementById('past-orders').style.display = 'none';

      // Remove active class from all tabs
      const tabs = document.querySelectorAll('.tab');
      tabs.forEach(tab => {
        tab.classList.remove('active-tab');
      });

      // Show the selected tab
      if (tab === 'new') {
        document.getElementById('new-orders').style.display = 'block';
        document.querySelectorAll('.tab')[0].classList.add('active-tab');
      } else if (tab === 'past') {
        document.getElementById('past-orders').style.display = 'block';
        document.querySelectorAll('.tab')[1].classList.add('active-tab');
      }
    }

    
       // JavaScript function to handle status update via AJAX
function updateStatus(orderId, statusType) {
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "adminaccept.php", true);
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4 && xhr.status === 200) {
            var response = JSON.parse(xhr.responseText);
            if (response.status === 'success') {
                alert(response.message);
                location.reload();
            }
        }
    };
    xhr.send("update_status=1&order_id=" + orderId + "&status_type=" + statusType);
}
  </script>

</body>
</html>