<?php
session_start();
require 'connection.php';

// Check if user ID is set in the session
if (!isset($_SESSION['user_id'])) {
    die("User ID is not set. Please log in.");
}

$userid = $_SESSION['user_id'];

// Fetch user details
$stmt = $pdo->prepare("SELECT _address, contact_number, points FROM user WHERE id = ?");
$stmt->execute([$userid]);
$user = $stmt->fetch();

if (!$user) {
    die("User details not found.");
}

$userPoints = $user['points']; // Not used now, since you will handle points in accepting orders
$totalAmount = 0;
$discountAmount = 0;

// Initialize variables to avoid warnings if not set
$packageId = 0;
$packageName = '';
$includedServices = [];
$additionalServices = [];

// Process the form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ensure that all necessary variables are set when the form is submitted
    if (isset($_POST['package_id'])) {
        $packageId = $_POST['package_id'];
    }

    if (isset($_POST['package_name'])) {
        $packageName = $_POST['package_name'];
    }

    if (isset($_POST['package_price'])) {
        $packagePrice = floatval($_POST['package_price']);
    }

    if (isset($_POST['included_services'])) {
        $includedServices = $_POST['included_services'];
    }

    if (isset($_POST['additional_services'])) {
        $additionalServices = $_POST['additional_services'];
    }

    // Fetch the service prices for the selected promo
    $stmt = $pdo->prepare("
        SELECT s.id AS service_id, s._name AS service_name, s._price AS service_price, s.minutes AS service_minutes
        FROM _service s
        JOIN promo_service ps ON s.id = ps.service_id
        WHERE ps.promo_id = ?
    ");
    $stmt->execute([$packageId]);
    $services = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Calculate the total price from services
    $totalServicePrice = 0;
    $totalServiceMinutes = 0;
    foreach ($services as $service) {
        $totalServicePrice += $service['service_price']; // Sum the prices of included services
        $totalServiceMinutes += $service['service_minutes'];
    }

    // Calculate discount: Total service price minus the promo price
    $discountAmount = $totalServicePrice - $packagePrice;
    if ($discountAmount < 0) {
        $discountAmount = 0; // Ensure discount is not negative
    }

    // Calculate the final total amount after applying the discount
    $totalAmount = $totalServicePrice - $discountAmount;

    // Fetch points reward and points cost for the selected promo
    $stmt = $pdo->prepare("SELECT points_reward, points_cost FROM promo WHERE id = ?");
    $stmt->execute([$packageId]);
    $promoDetails = $stmt->fetch();

    $pointsReward = $promoDetails['points_reward']; // Points earned for this promo
    $pointsCost = $promoDetails['points_cost'];     // Points cost for the promo (if any)

    // Check if user has enough points to cover the points cost
    if ($pointsCost > $userPoints) {
        // Redirect to packages page if points cost is greater than user points
        header("Location: /packages.php");
        exit;
    }

    // Prepare order data to store in `_transaction`
    $orderData = [
        'user_id' => $userid,
        'package_name' => $packageName,
        'package_price' => $packagePrice,
        'included_services' => $includedServices,
        'additional_services' => $additionalServices,
        'total_amount' => $totalAmount,
        'total_minutes' => $totalServiceMinutes,
        'address' => $user['_address'],
        'contact_number' => $user['contact_number'],
        'order_date' => date('Y-m-d H:i:s'),
        'discount_amount' => $discountAmount, // Discount applied to the order
    ];

    // Convert order data to JSON format for storage
    $orderTransaction = json_encode($orderData, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

    // Store the order in `_statement` table
    $stmt = $pdo->prepare("INSERT INTO _statement (user_id, promo_id, _transaction) 
                           VALUES (?, ?, ?)");
    $stmt->execute([$userid, $packageId ? $packageId : null, $orderTransaction]);
}
?>






<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <!-- <meta name="viewport" content="width=device-width, initial-scale=1.0"> -->
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>Order</title>
    <style>
/* Reset styling */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

/* Body styling */
body {
    font-family: 'Arial', sans-serif;
    display: flex;
    justify-content: center;
    align-items: flex-start;
    min-height: 100vh;
    margin: 0;
    overflow-y: auto;
    background-color: #f9f9f9; /* Light gray background for contrast */
    padding-top: 30px;
}

/* Main container styling */
.container {
    width: 90%;
    max-width: 420px;
    background-color: #ffffff; /* Pure white for the main container */
    border-radius: 15px;
    padding: 25px;
    margin-bottom: 30px;
    box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.1); /* Soft shadow for depth */
}

/* Header styling */
h2 {
    text-align: center;
    color: #D32F2F; /* Bold red for headers */
    margin-bottom: 15px;
    font-size: 1.6em;
}
 .title h2{
    position: relative;
    bottom: 30px;
 }
/* Button styling */
button, #addressBtn, #contactBtn {
    width: 100%;
    margin: 10px 0;
    padding: 12px;
    background: linear-gradient(135deg, #D32F2F, #FF5A5A); /* Gradient red */
    color: #ffffff; /* White text */
    border: none;
    border-radius: 10px;
    cursor: pointer;
    font-weight: bold;
    font-size: 1em;
    transition: all 0.3s ease;
}

button:hover, #addressBtn:hover, #contactBtn:hover {
    background: linear-gradient(135deg, #B71C1C, #D32F2F); /* Darker gradient on hover */
    transform: scale(1.03); /* Slight scale effect on hover */
}

/* Order list styling */
.order-list {
    background-color: #FFF5F5; /* Soft red background */
    padding: 20px;
    border-radius: 10px;
    margin-top: 20px;
    box-shadow: 0px 3px 10px rgba(0, 0, 0, 0.08); /* Subtle shadow */
}

.order-list h2 {
    color: #D32F2F; /* Bold red for order list heading */
    font-size: 1.4em;
    margin-bottom: 12px;
}

.order-list ul {
    list-style-type: none;
    padding: 0;
}

.order-list li {
    color: #333;
    font-size: 1em;
    margin: 6px 0;
    padding-left: 20px;
    position: relative;
}

.order-list li::before {
    content: "•";
    color: #D32F2F; /* Red bullet points */
    position: absolute;
    left: 0;
}

/* Summary section styling */
.summary {
    background: linear-gradient(135deg, #FF5A5A, #D32F2F); /* Bold gradient */
    padding: 15px;
    border-radius: 10px;
    font-weight: bold;
    color: #ffffff; /* White text for contrast */
    margin-top: 20px;
    box-shadow: 0px 3px 10px rgba(0, 0, 0, 0.08);
}

/* Back button styling */
.back-button {
    display: inline-flex;
    align-items: center;
    margin-bottom: 20px;
}

.back-button a {
    text-decoration: none;
    color: #D32F2F;
    font-size: 1em;
    font-weight: bold;
}

.back-button img {
    position: relative;
    top: 49px;
    left: 25px;
}

.icon {
    width: 25px;
    height: 25px;
    margin-right: 8px;
}

/* Confirm button styling */
#confirmBtn {
    background: linear-gradient(135deg, #D32F2F, #FF5A5A); /* Red gradient for confirm button */
    color: #ffffff; /* White text */
    font-size: 1.1em;
    font-weight: bold;
    padding: 15px;
    border-radius: 10px;
    margin-top: 25px;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.1); /* Soft shadow */
}

#confirmBtn:hover {
    background: linear-gradient(135deg, #B71C1C, #D32F2F); /* Darker gradient on hover */
    transform: translateY(-3px); /* Lift effect on hover */
}

/* Discount container styling */
.discount-container {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    margin: 1.2rem 0;
    font-family: Arial, sans-serif;
    color: #333;
    font-size: 1em;
}


/* Container styling */
.discount-container {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    margin: 1rem 0;
    font-family: Arial, sans-serif;
    color: #333;
}

/* Label styling */
.discount-label {
    font-size: 1.1rem;
    font-weight: bold;
    color: red;
    margin-bottom: 0.5rem;
}

/* Select dropdown styling */
.discount-dropdown {
    position: relative;
    width: 100%;
}

.fancy-select {
    width: 100%;
    padding: 0.75rem 1rem;
    border-radius: 8px;
    border: 2px solid #FF6B6B;
    background: linear-gradient(135deg, #FF6B6B 0%, #FF9478 100%);
    color: red;
    font-size: 1rem;
    font-weight: bold;
    cursor: pointer;
    appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none;
    transition: all 0.3s ease;
}

/* Dropdown arrow customization */
.fancy-select::after {
    content: "▼";
    position: absolute;
    top: 50%;
    right: 1rem;
    transform: translateY(-50%);
    color: red;
    pointer-events: none;
}

/* Hover and focus effects */
.fancy-select:hover,
.fancy-select:focus {
    background: linear-gradient(135deg, #FF9478 0%, #FF6B6B 100%);
    border-color: #FF9478;
    outline: none;
}


    </style>
</head>
<body>
<div class="container">
    <div class="back-button">
        <a href="packages.php" onclick="goBack()">
            <img src="back.png" alt="Back Icon" class="icon">
        </a>
    </div>
    <h2 class="title">Your Order</h2>
    <button id="addressBtn"><?php echo htmlspecialchars($user['_address']); ?></button>
    <button id="contactBtn"><?php echo htmlspecialchars($user['contact_number']); ?></button>

    <!-- Order List Section -->
    <form action="userdashboard.php" method="post">
        <input type="hidden" name="package_id" id="packageIdHidden" value="<?php echo htmlspecialchars($packageId); ?>">
        <input type="hidden" name="package_name" id="packageNameHidden" value="<?php echo htmlspecialchars($packageName); ?>">
        <input type="hidden" name="package_price" id="packagePriceHidden" value="<?php echo htmlspecialchars($packagePrice); ?>">

        <div class="order-list">
            <h2>Order List</h2>
            <p><strong>Package Name:</strong> <span id="displayPackageName"><?php echo htmlspecialchars($packageName); ?></span></p>
            <p><strong>Price:</strong> ₱<span id="displayPackagePrice"><?php echo htmlspecialchars($totalServicePrice); ?></span></p>
            
            <p><strong>Included Services:</strong></p>
            <ul id="includedServicesList">
                <?php foreach ($services as $service): ?>
                    <li><?php echo htmlspecialchars($service['service_name']) . " - ₱" . number_format($service['service_price'], 2) . ' - ' . $service['service_minutes'] . ' Minutes'; ?></li>
                <?php endforeach; ?>
            </ul>
        </div>

        <!-- Discount Section (Directly applied) -->
        <div class="summary">
            <p><strong>Approximate Time:</strong> <?php echo $totalServiceMinutes . ' minutes'; ?> </p>
            <p><strong>Discount:</strong> ₱<?php echo number_format($discountAmount, 2); ?></p>
            <p><strong>Total Amount After Discount:</strong> ₱<span id="totalAmount"><?php echo number_format($totalAmount, 2); ?></span></p>

            <!-- Points Reward and Points Cost -->
            <p><strong>Points Reward:</strong> <?php echo htmlspecialchars($pointsReward); ?> points</p>
            <p><strong>Points Cost:</strong> <?php echo htmlspecialchars($pointsCost); ?> points</p>
        </div>

        <button type="submit" id="confirmBtn">Confirm</button>
    </form>
</div>





<script>
let packagePrice = <?php echo $packagePrice; ?>;
let deliveryFee = 50;
let totalAmount = packagePrice + deliveryFee;
let pointsEarned = Math.floor(totalAmount / 100);
let userPoints = <?php echo $userPoints; ?>;
const additionalServices = new Map();
const discountSelect = document.getElementById('discountSelect');
const totalAmountHidden = document.getElementById('totalAmountHidden'); // Hidden field to store the updated total amount

function confirmOrder() {
    const form = document.createElement("form");
    form.method = "POST";
    form.action = "track.php";

    // Add hidden inputs for package details and total amount
    const packageInputs = {
        package_id: document.getElementById("packageIdHidden").value,
        package_name: document.getElementById("packageNameHidden").value,
        package_price: packagePrice,
        total_amount: totalAmountHidden.value // Use the updated totalAmount with discount applied
    };

    for (const [name, value] of Object.entries(packageInputs)) {
        const input = document.createElement("input");
        input.type = "hidden";
        input.name = name;
        input.value = value;
        form.appendChild(input);
    }

    // Add hidden inputs for each selected additional service
    additionalServices.forEach((price, service) => {
        const serviceInput = document.createElement("input");
        serviceInput.type = "hidden";
        serviceInput.name = "additional_services[]";
        serviceInput.value = service;
        form.appendChild(serviceInput);
    });

    document.body.appendChild(form);
    form.submit();
}

// Check package details on confirm button click
document.getElementById('confirmBtn').addEventListener('click', function(event) {
    const packageId = document.getElementById("packageIdHidden").value;
    const packageName = document.getElementById("packageNameHidden").value;
    const packagePrice = document.getElementById("packagePriceHidden").value;

    if (!packageId || !packageName || !packagePrice) {
        event.preventDefault();
        alert("Package details are not set.");
    } else {
        confirmOrder(); // Call confirmOrder if details are valid
    }
});
</script>



</body>
</html>
