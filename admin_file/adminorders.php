<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Order Confirmation</title>
    <style>
        /* General Styling */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            background-color: #f5f5f5;
            padding: 20px;
            display: flex;
            justify-content: center;
        }

        /* Container Styling */
        .orders-container {
            max-width: 800px;
            width: 100%;
        }

        .order {
            background-color: #fff;
            border: 2px solid #ff4d4d;
            border-radius: 10px;
            margin-bottom: 20px;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .order h2 {
            color: #ff4d4d;
            margin-bottom: 10px;
        }

        .order-details p {
            margin: 5px 0;
            color: #666;
        }

        .order-items h3 {
            color: #ff4d4d;
            margin-top: 15px;
        }

        .item {
            display: flex;
            justify-content: space-between;
            margin: 5px 0;
        }

        .item-name {
            color: #333;
        }

        .total {
            font-weight: bold;
            color: #333;
            margin-top: 10px;
        }

        /* Buttons Styling */
        .actions {
            margin-top: 15px;
            display: flex;
            justify-content: space-around;
        }

        button {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            font-weight: bold;
            cursor: pointer;
            transition: transform 0.2s;
        }

        button:hover {
            transform: scale(1.05);
        }

        .confirm-btn {
            background-color: #ff4d4d;
            color: white;
        }

        .cancel-btn {
            background-color: white;
            color: #ff4d4d;
            border: 2px solid #ff4d4d;
        }

        .cancel-btn:hover {
            background-color: #ffe6e6;
        }
    </style>
</head>
<body>
    <div class="orders-container">
        <!-- Order 1 -->
        <div class="order" id="order1">
            <h2>Order #12345</h2>
            <div class="order-details">
                <p><strong>Customer:</strong> John Doe</p>
                <p><strong>Order Date:</strong> November 7, 2024</p>
                <p><strong>Delivery Address:</strong> 123 Main St, City</p>
            </div>
            <div class="order-items">
                <h3>Items</h3>
                <div class="item">
                    <span class="item-name">Red Shirt</span>
                    <span class="item-quantity">Quantity: 2</span>
                    <span class="item-price">$30</span>
                </div>
                <div class="item">
                    <span class="item-name">White Sneakers</span>
                    <span class="item-quantity">Quantity: 1</span>
                    <span class="item-price">$50</span>
                </div>
                <div class="total">
                    <strong>Total:</strong> $110
                </div>
            </div>
            <div class="actions">
                <button onclick="confirmOrder('Order #12345')" class="confirm-btn">Confirm Order</button>
                <button onclick="cancelOrder('Order #12345')" class="cancel-btn">Cancel Order</button>
            </div>
        </div>

        <!-- Order 2 -->
        <div class="order" id="order2">
            <h2>Order #12346</h2>
            <div class="order-details">
                <p><strong>Customer:</strong> Jane Smith</p>
                <p><strong>Order Date:</strong> November 7, 2024</p>
                <p><strong>Delivery Address:</strong> 456 Another St, City</p>
            </div>
            <div class="order-items">
                <h3>Items</h3>
                <div class="item">
                    <span class="item-name">Blue Jeans</span>
                    <span class="item-quantity">Quantity: 1</span>
                    <span class="item-price">$45</span>
                </div>
                <div class="item">
                    <span class="item-name">White T-Shirt</span>
                    <span class="item-quantity">Quantity: 3</span>
                    <span class="item-price">$15</span>
                </div>
                <div class="total">
                    <strong>Total:</strong> $90
                </div>
            </div>
            <div class="actions">
                <button onclick="confirmOrder('Order #12346')" class="confirm-btn">Confirm Order</button>
                <button onclick="cancelOrder('Order #12346')" class="cancel-btn">Cancel Order</button>
            </div>
        </div>
    </div>

    <script>
        function confirmOrder(orderId) {
            alert(orderId + " confirmed successfully!");
            // Code to handle order confirmation for this specific order
        }

        function cancelOrder(orderId) {
            const confirmation = confirm("Are you sure you want to cancel " + orderId + "?");
            if (confirmation) {
                alert(orderId + " cancelled.");
                // Code to handle order cancellation for this specific order
            }
        }
    </script>
</body>
</html>
