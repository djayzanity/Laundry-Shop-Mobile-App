<?php
// Include database connection
include 'connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pickupDate = $_POST['pickupDate'];
    $pickupTime = $_POST['pickupTime'];
    $deliveryDate = $_POST['deliveryDate'];
    $deliveryTime = $_POST['deliveryTime'];
    $address = $_POST['address']; // Get address from the form

    try {
        // Prepare an SQL statement for execution
        $stmt = $pdo->prepare("INSERT INTO schedule (pickup_date, pickup_time, delivery_date, delivery_time, address) VALUES (:pickup_date, :pickup_time, :delivery_date, :delivery_time, :address)");

        // Bind parameters to the SQL query
        $stmt->bindParam(':pickup_date', $pickupDate);
        $stmt->bindParam(':pickup_time', $pickupTime);
        $stmt->bindParam(':delivery_date', $deliveryDate);
        $stmt->bindParam(':delivery_time', $deliveryTime);
        $stmt->bindParam(':address', $address);

        // Execute the prepared statement
        if ($stmt->execute()) {
            echo "<script>alert('Schedule set successfully!'); window.location.href = 'thankyou.php';</script>";
        } else {
            echo "<script>alert('Error setting schedule. Please try again.');</script>";
        }
    } catch (PDOException $e) {
        echo "<script>alert('Database error: " . $e->getMessage() . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pickup and Delivery Schedule</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f3f4f6;
            padding: 0;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            overflow: hidden;
        }

        .container {
            max-width: 400px;
            width: 90%;
            padding: 30px;
            position: relative;
            transition: transform 0.3s ease;
        }

        h1 {
            color: #D32F2F;
            text-align: center;
            margin-bottom: 20px;
            font-size: 28px;
            font-weight: 600;
        }

        .tabs {
            display: flex;
            justify-content: space-around;
            margin-bottom: 20px;
            border-bottom: 2px solid #D32F2F;
        }

        .tab {
            padding: 10px 20px;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .tab.active {
            color: #fff;
            background-color: #D32F2F;
            border-radius: 20px 20px 0 0;
        }

        .form-group {
            margin-bottom: 20px;
            position: relative;
        }

        label {
            font-weight: 600;
            display: block;
            margin-bottom: 5px;
            color: #444;
        }

        input[type="date"],
        input[type="time"],
        input[type="text"] {
            width: 100%;
            padding: 12px;
            border: 2px solid #D32F2F;
            border-radius: 8px;
            font-size: 16px;
            background-color: #fff;
            position: relative;
            left: -15px;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
            outline: none;
        }

        input[type="date"]:focus,
        input[type="time"]:focus,
        input[type="text"]:focus {
            border-color: #B71C1C;
            box-shadow: 0 0 8px rgba(179, 28, 28, 0.4);
        }

        button {
            width: 100%;
            padding: 14px;
            background-color: #D32F2F;
            color: #fff;
            font-size: 18px;
            font-weight: bold;
            border: none;
            border-radius: 20px;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.3s ease;
            box-shadow: 0 6px 15px rgba(211, 47, 47, 0.3);
        }

        button:hover {
            background-color: #B71C1C;
            transform: translateY(-2px);
        }

        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
            color: #777;
            font-weight: 400;
        }

        /* Hide all tab content by default */
        .tab-content {
            display: none;
        }

        /* Show active tab content */
        .tab-content.active {
            display: block;
        }
    </style>
</head>
<body>
<div class="container">
        <h1>Schedule Pickup & Delivery</h1>
        <form method="POST" id="scheduleForm">
            <div class="form-group">
                <label for="address">Delivery Address</label>
                <input type="text" id="address" name="address" required placeholder="Enter your address">
            </div>

            <div class="tabs">
                <div class="tab active" data-tab="pickup">Pickup</div>
                <div class="tab" data-tab="delivery">Delivery</div>
            </div>

            <div id="pickup" class="tab-content active">
                <div class="form-group">
                    <label for="pickupDate">Pickup Date</label>
                    <input type="date" id="pickupDate" name="pickupDate" required>
                </div>
                <div class="form-group">
                    <label for="pickupTime">Pickup Time</label>
                    <input type="time" id="pickupTime" name="pickupTime" required>
                </div>
            </div>

            <div id="delivery" class="tab-content">
                <div class="form-group">
                    <label for="deliveryDate">Delivery Date</label>
                    <input type="date" id="deliveryDate" name="deliveryDate" required>
                </div>
                <div class="form-group">
                    <label for="deliveryTime">Delivery Time</label>
                    <input type="time" id="deliveryTime" name="deliveryTime" required>
                </div>
            </div>

            <button type="submit">Set Schedule</button>
        </form>
        <div class="footer">Your convenience is our priority!</div>
    </div>


    <script>
        // Tab functionality
        const tabs = document.querySelectorAll('.tab');
        const tabContents = document.querySelectorAll('.tab-content');

        tabs.forEach(tab => {
            tab.addEventListener('click', () => {
                tabs.forEach(t => t.classList.remove('active'));
                tabContents.forEach(content => content.classList.remove('active'));

                tab.classList.add('active');
                const tabId = tab.getAttribute('data-tab');
                document.getElementById(tabId).classList.add('active');
            });
        });

        // Ensure delivery date is after pickup date
        const pickupDate = document.getElementById('pickupDate');
        const deliveryDate = document.getElementById('deliveryDate');

        pickupDate.addEventListener('change', function() {
            const minDeliveryDate = new Date(this.value);
            minDeliveryDate.setDate(minDeliveryDate.getDate() + 1); // Set minimum delivery date to one day after pickup
            deliveryDate.min = minDeliveryDate.toISOString().split('T')[0];
        });
    </script>
</body>
</html>
