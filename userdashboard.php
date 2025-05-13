<?php
// At the start of the file, before any output is sent to the browser
if (isset($_GET['page'])) {
    $page = $_GET['page'];
    switch ($page) {
        case 'home':
            header('Location: userdashboard.php');
            exit();
        case 'packages':
            header('Location: packages.php');
            exit();
        case 'profile':
            header('Location: profile.php');
            exit();
        default:
            // Redirect to the dashboard if the page parameter is not recognized
            header('Location: userdashboard.php');
            exit();
    }
}
?>

<?php
session_start();
require 'connection.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user details
$stmt = $pdo->prepare('SELECT full_name, points FROM user WHERE id = ?');
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user) {
    $_SESSION['full_name'] = htmlspecialchars($user['full_name']);
    
    // Calculate available points
    $points = $user['points'];

    // Display points earned/deducted if set, then clear session data
    if (isset($_SESSION['points_earned']) || isset($_SESSION['points_deducted'])) {
        $pointsEarned = $_SESSION['points_earned'] ?? 0;
        $pointsDeducted = $_SESSION['points_deducted'] ?? 0;
        unset($_SESSION['points_earned']);
        unset($_SESSION['points_deducted']);
    }

    // Update points in the database
    $stmt = $pdo->prepare("UPDATE user SET points = ? WHERE id = ?");
    $stmt->execute([$points, $user_id]);
} else {
    echo "User not found.";
    exit();
}

// Fetch orders directly from the _statement table for the logged-in user
$stmt = $pdo->prepare("SELECT * FROM _statement WHERE user_id = ? AND delivered_at IS NULL");
$stmt->execute([$user_id]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Count the orders
$orderCount = count($orders);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <!-- <meta name="viewport" content="width=device-width, initial-scale=1.0"> -->
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">

    
    <!-- Google Font Linking -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0&icon_names=arrow_forward" />

    <link rel="stylesheet" href="/css/userdash.css">

    <!-- Linking Swiper -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <title>User Dashboard</title>

    <style>

@import url('https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&family=Raleway:ital,wght@0,100..900;1,100..900&display=swap');
@import url('https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&family=Nunito:ital,wght@0,200..1000;1,200..1000&family=Raleway:ital,wght@0,100..900;1,100..900&display=swap');
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
  font-family: 'Arial';
}

body {
  font-family: Arial, sans-serif;
  background-color: #f5f5f5;
  transition: opacity 0.5s ease-in-out;
    opacity: 1;
    overflow-y: auto;
}

body.fade-out {
  opacity: 0;
}

.container {
  width: 100%;
  max-width: 1000px; /* Ensures max width */
  margin: 0 auto;
  padding-left: 10px;
  padding-right: 10px;
  padding-top: 10px;
  margin-bottom: 70px;

}

/* Promo Section */
.promo-section {
  position: relative;
  width: 100%;
  height: 250px; /* Example height */
  display: flex;
  align-items: center;
  justify-content: center;
  background-color: #ffffff;
  border-radius: 10px;
  border: none;
  margin-bottom: 10px;
  overflow: hidden;
  height: auto;
}

/* Promo Image Styling */
.promo-image {
  display: inline-block;
  width: 100%;
  max-width: 1000px;
  height: 100%;
  border-radius: 10px;
  object-fit: cover;
  overflow: hidden;
}

/* Promo Text Styling */
.promo-text {
  position: absolute;
  top: 60%;
  left: 60%;
  transform: translate(-50%, -20%);
  color: white;
  padding: 15px;
  text-align: right;
  width: 85%;
  max-width: 600px;
}

.promo-text h2 {
  position: relative;
  bottom: 100px ;
  font-size: 1.7rem;
  font-family: "Nunito", sans-serif;
  line-height: 1.2;
  color: white;
  
}

.highlight-name {
  color: red;
  font-family: "Raleway", serif;  
  font-weight: 800; 
  text-transform: capitalize;
  position: relative;
  top: -2px;
}
.highlight-name span{
  font: 1.7rem;
  position: relative;
  top: -2px;

}

.wave {
  font-size: 1.5rem; /* Adjust the size of the wave */
  /* Add some space between the name and the wave */
  display: inline-block; /* Necessary for the transform property */
  animation: wave-animation 1.2s infinite;
  position: relative;
  top: -2px;
}

/* Keyframes for Wave Animation */
@keyframes wave-animation {
  0% { transform: rotate(0deg); }
  10% { transform: rotate(14deg); }
  20% { transform: rotate(-8deg); }
  30% { transform: rotate(14deg); }
  40% { transform: rotate(-4deg); }
  50% { transform: rotate(10deg); }
  60% { transform: rotate(0deg); }
  100% { transform: rotate(0deg); }
}

.greet{
  position: relative;
  top: -222px;
  font-family: "Nunito", sans-serif;
  font-size: 3rem;
  text-align: right;
  line-height: 1;
  margin-bottom: 8px;
  margin-right: -6px ;

}

.highlight {
  color: #e53935;
  font-weight: bold;
}

.view-package {
  position: relative;
  top: -100px;
  display: inline-block;
  margin-top: 10px;
  color: #e53935;
  text-decoration: none;
  font-size: 1rem;
  text-decoration: underline;
}

/* Loyalty Points Section */
.loyalty-section {
  background-color: #ffffff;
  padding: 10px;
  border-radius: 10px;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}

.loyalty-section h3 {
  font-size: 1.25rem;
  margin-bottom: 5px;
}

.loyalty-box {
  display: flex;
  justify-content: space-between;
  align-items: center;
  background-color: #ff2e2e;
  padding: 20px;
  border-radius: 10px;
  color: #ffffff;
}

.points p {
  font-size: 5px;
  font-weight: bold;
}
.points h1 {
  font-size: 3rem;
  margin-bottom: 5px;
}

.loyalty-section p {
  font-size: 10px;
  margin-bottom: 5px;
}

.redeem-btn {
  background-color: #FABC3F;
  color: white;
  border: none;
  padding: 10px 20px;
  border-radius: 5px;
  font-size: 1rem;
  cursor: pointer;
}


/* SERVICES */
.serve-container {
  display: flex;
  align-items: center;
  justify-content: center;
  min-height: 38vh;
  background-color: linear-gradient(#ECEFFE, #CED6FB);

}

.card-container h1{
  margin-top: 5px;
}

.card-wrapper {
  max-width: 1100px;
  margin: 0 60px 6px;
  padding: 20px 2px;
  overflow: hidden;
}

.card-list .card-item {
  list-style: none;
}

.card-list .card-item .card-link {
  user-select: none;
  display: block;
  background: white;
  padding: 18px;
  border-radius: 20px;
  text-decoration: none;
  border: 2px solid transparent;
  box-shadow: 0 10px rgba(0, 0, 0, 0, 0.05);
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.card-list .card-link .card-image {
  width: 100%;
  height: 164px;
  aspect-ratio: 16 / 9;
  object-fit: cover;
  margin-left: 1px;
  border-radius: 5px;
}

.card-list .card-link .badge {
  color: #5372F0;
  padding: 8px 16px;
  font-size: 0.95rem;
  font-weight: 500;
  margin: 16px 0 18px;
  background: #DDE4FF;
  width: fit-content;
  border-radius: 50px;
}
.card-list .card-link .badge.wash {
  color: red;
  background: #ffd47e;
}

.card-list .card-link .badge.dryer {
  color: red;color: red;
  background: #ffd47e;
}

.card-list .card-link .badge.stain {
  color: red;
  background: #ffd47e;
}

.card-list .card-link .badge.ironing {
  color: red;
  background: #ffd47e;
}


.card-list .card-link .card-title {
  font-size: 12px;
  color: black;
  font-weight: 600;
   
}

.card-list .card-link .card-button {
  height: 35px;
  width: 35px;
  color: red;
  border-radius: 50%;
  margin: 30px 0 5px;
  background: none;
  cursor: pointer;
  border: 2px solid grey;
  transform: rotate(-45deg);
}


.swiper-pagination, .swiper-button-next, .swiper-button-prev {
  display: block;
  top: 100px;
}



.order-count h2{
  font-size: 20px;
  margin-bottom: 5px;
  margin-left: 60px;
}

.order-card {
  display: flex;
  align-items: center;
  justify-content: space-between;
  border: 2px solid red; /* Light purple border */
  border-radius: 10px;
  background-color: #f5f5f5;
  padding:20px;
  margin-left: 55px;
  margin-right: 55px;
  margin-bottom: 100px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.order-icon img {
  width: 50px;
  height: 50px;
  border-radius: 50%;
  background-color: red;
  padding: 5px;
}

.order-details {
  display: flex;
  justify-content: space-between;
  width: 24%;
  position: relative;
  right: 64%;
}

.order-info h3 {
  font-size: 16px;
  font-weight: bold;


}

.order-info .order-status {
  color: red;
  font-size: 12px;
}

.stat {
  position: relative;
  top: 10px;
  left: 20px;
}

.order-price {
  text-align: right;
}

.order-price h2 {
  font-size: 22px;
  color: black;
  margin-right: 20px;
}

.order-price p {
  font-size: 12px;
  color: gray;
  margin-right: 20px;
}


.bottom-nav {
  position: fixed;
  bottom: -2px;
  width: 100%;
  background-color: rgba(255, 0, 0, 0.7);
  display: flex;
  justify-content: space-around;
  align-items: center;
  padding: 15px 0;
  border-top-left-radius: 20px;
  border-top-right-radius: 20px;
  box-shadow: 0 -4px 6px rgba(0, 0, 0, 0.1);
  margin-top: 70px;
  z-index: 100;
}

.nav-item {
  text-align: center;
  flex-grow: 1;
}

.nav-item img {
  width: 22px;
  height: 22px;
}

.no-order {
  margin-left: 70px;
}


/* General Styling */
.statement-count h2 {
  font-size: 1.3rem;
  font-weight: 600;
  color: #333;
  margin-bottom: 1rem;
  margin-left: 10px;
}

.no-statement {
  color: #777;
  font-style: italic;
  margin-left: 10px;
}

/* Statement Card Styling */
.statement-card {
  background-color: #fff;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  border-radius: 8px;
  padding: 16px;
  display: flex;
  align-items: center;
  margin-bottom: 16px;
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.statement-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
}

/* Statement Icon Styling */
.statement-icon img {
  width: 48px;
  height: 48px;
}

/* Statement Details Styling */
.statement-details {
  flex-grow: 1;
  padding-left: 16px;
}

.statement-info h3 {
  font-size: 1.125rem;
  font-weight: 700;
  color: #2c3e50;
  margin-bottom: 4px;
}

.statement-info p {
  font-size: 0.875rem;
  color: #555;
  margin: 2px 0;
}


.order-card {
    display: flex;
    align-items: center;
    background-color: #f9f9f9;
    padding: 16px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    margin-bottom: 16px;
}

.order-icon {
    margin-right: 16px;
}

.order-icon img {
    width: 40px;
    height: 40px;
}

.order-details {
    flex-grow: 1;
}

.order-details h3 {
    font-size: 16px;
    font-weight: bold;
    margin: 0;
}

#order-status {
    color: #00aaff;
    font-size: 14px;
    margin: 4px 0 0;
    position: relative;
    left: 178px;
}

.order-price-date {
    text-align: right;
}

.order-price {
    font-size: 16px;
    font-weight: bold;
}

.order-date {
    color: #888;
    font-size: 14px;
    margin-top: 4px;
}

.icon-order{
  position: relative;
  left: 77%;
  width: 55px;
  height: 55px;
}




/* Media Queries for Smaller Devices */

/* For screens with width up to 768px (Tablets and small devices) */
@media (max-width: 720px) {
  .promo-text h2 {
    font-size: 1.4rem;
  }

  .promo-text {
    top: 30%;
    width: 95%;
    left: 50%;
    transform: translate(-50%, -30%);
  }

  .service-card img {
    width: 90px;
    height: 90px;
    margin: 14px 325px 50px;
  }

  .promo-section {
    padding: 5px;
  }
  .view-package {
    font-size: 0.7rem;
  }

  .promo-image {
    height: auto;
  }

  .loyalty-box {
    flex-direction: column;
    text-align: center;
  }

  .points h1 {
    font-size: 2.5rem;
  }

  .redeem-btn {
    margin-top: 10px;
  }
  .order-count{
    font-size: 20px;
    margin-bottom: 5px;
    margin-left: -40px;
  }

  .order-card {
    width: 90%;
    margin-left: 16px;
    padding: 12px 12px 1px 12px;
  }

  .order-icon img  {
    width: 30px;
    height: 30px;
    margin-bottom: 12px;
  }
  .order-details {
    font-size: 12px;
  }
  .order-info h3 {
    font-size: 12px;
    margin-top: 13px;
  }
  .order-status {
    font-size: 12px;
  }
  .order-price h2 {
    font-size:18px;
    margin-top: 10px;
  }
  .order-price p {
    font-size: 10px;
    margin-bottom: 15px;
  }

  .statement-card {
    flex-direction: column;
    align-items: flex-start;
}

.statement-icon {
    margin-bottom: 16px;
}

.statement-details {
    padding-left: 0;
}

.statement-info h3 {
    font-size: 1rem;
}

.statement-info p {
    font-size: 0.75rem;
}

.icon-order{
  position: relative;
  left: 100%;
  top: -6px;
  width: 38px;
  height: 38px;
}

.stat {
  position: relative;
  top: 10px;
  left: 20px;
}


.card-container h1{
  margin-top: 5px;
}
}


/* For mobile devices (screens with width less than 480px) */
@media (max-width: 480px) {
  .promo-text h2 {
    top: -73px;
    font-size: 20px;
    padding-top: 203px;
  }

  .promo-text {
    top: 30%;
    width: 95%;
    left: 50%;
    transform: translate(-50%, -30%);
  }

  .view-package {
    font-size: 0.7rem;
  }

  .promo-section {
    padding: 5px;
  }

  .promo-image {
    width: 100%;
    height: auto;
  }

  .loyalty-box {
    flex-direction: row;
    text-align: center;
  }
  
  .loyalty-box p h1{
    font-size: 10px;
    margin-bottom: 70px;

  }

  .points h1 {
    font-size: 20px;
    margin-right: 50px;
  }

  .redeem-btn {
    margin-top: 10px;
  }

  .greet{
    font-size: 1.3rem;
    top: -91px;
    right: 30px;
    line-height: normal;
    text-align: right;
    padding-left: 100px;
    margin-left: 44px;

  }
  .highlight-name{
    top: 2px;
  }

  .icon-order img {
    margin-right: -200px;
  }

  .icon-order {
    position: relative;
        left: 100%;
        top: -6px;
        width: 34px;
        height: 34px;
  }

  
.order-details h3 {
    font-size: 15px;
    font-weight: bold;
    margin: 0;
    position: relative;
    left: 129px;
    bottom: 8px;

}


.stat {
  position: relative;
  top: 7px;
  left: 126px;
  font-size: 13px;
  top: -3px;
}


.card-container h1{
  margin-top: 5px;
  margin-left: 15px;
}
  
}






/* For extra small mobile devices (screens width less than 397px) */
@media (max-width: 397px) {
  .promo-text h2 {
    font-size: 11px;
    padding-top: 10px;
  }

  .promo-text {
    top: 59%;
    width: 95%;
    left: 50%;
    text-align: right;
    transform: translate(-50%, -35%);
  }
  .view-package {
    font-size: 0.7rem;
  }

  .promo-section {
    padding: 5px;
  }

  .promo-image {
    height: auto;
  }

  .loyalty-box {
    flex-direction: flex-start;
    text-align: center;
  }

  .points h1 {
    font-size: 1.5rem;
    text-align: left;
    padding-left: 5px;
  }


  .redeem-btn {
    margin-top: 10px;
    margin-bottom: 10px;
    width: 100px;
    padding: 5px 3px 5px 3px;

  }
  .bottom-nav {
    padding: 10px 0;
}

.nav-item img {
    width: 24px;
    height: 24px;
}

}
  
    </style>
</head>
<body>
    <div class="container">
        <!-- Top Section -->
        <div class="promo-section">
            <img src="laundrytop.png" alt="Washing Machine" class="promo-image">
            <div class="promo-text">
            <h1 class="greet">Welcome to E&Q Laundry
                <span class="highlight-name"><?php echo htmlspecialchars($_SESSION['full_name']); ?></span>

              

            </h1>
            <h2>Discover our <span class="highlight">Quality Services</span><br>designed to exceed your expectations.</h2>
            </div>
        </div>

<!-- Loyalty Points Section -->
<div class="loyalty-section">
    <h3>Your loyalty points</h3>
    <p>Collect points by purchasing our services</p>
    <div class="loyalty-box">
        <div class="points">
            <p>Available Points</p>
            <h1><?= ($points > 0) ? $points : '--'; ?></h1>
        </div>
    </div>
</div>

<!-- Services Slider Section -->
<div class="serve-container">
          
            <div class="card-container swiper">
            <h1>Services</h1>
                <div class="card-wrapper">
                    <ul class="card-list swiper-wrapper">
                        <!-- Add all card items here as Swiper slides -->
                        <li class="card-item swiper-slide">
                            <a href="#" class="card-link">
                                <img src="wash.jpeg" alt="wash" class="card-image">
                                <p class="badge wash">Wash</p>
                                <h2 class="card-title">Get a deep, gentle clean for all fabrics with our efficient washers.</h2>
                            </a>
                        </li>
                        <li class="card-item swiper-slide">
                            <a href="#" class="card-link">
                                <img src="dryer1.jpg" alt="Dryer" class="card-image">
                                <p class="badge dryer">Dryer</p>
                                <h2 class="card-title">Fast, thorough drying that leaves clothes soft and ready to wear.</h2>
                            </a>
                        </li>
                        <li class="card-item swiper-slide">
                            <a href="#" class="card-link">
                                <img src="stainremover1.jpg" alt="stainremover" class="card-image">
                                <p class="badge stain">Stain Remover</p>
                                <h2 class="card-title">Special cleaning techniques to remove tough stains like grease, ink, or wine.</h2>
                            </a>
                        </li>
                        <li class="card-item swiper-slide">
                            <a href="#" class="card-link">
                                <img src="ironing1.jpeg" alt="ironing" class="card-image">
                                <p class="badge ironing">Ironing</p>
                                <h2 class="card-title">Professional ironing for shirts, pants, and delicate fabrics.</h2>
                            </a>
                        </li>
                        <li class="card-item swiper-slide">
                            <a href="#" class="card-link">
                                <img src="fabcon.jpeg" alt="Fabric" class="card-image">
                                <p class="badge ironing">Fabric Softening</p>
                                <h2 class="card-title">Use of fabric softeners for extra-soft and fragrant clothes.</h2>
                            </a>
                        </li>
                    </ul>
                </div>
               
            </div>
        </div>

        <div class="statement-count mb-4">
    <h2 class="text-lg font-semibold">Your Active Orders (<?= htmlspecialchars($orderCount) ?>)</h2>
</div>

<?php if ($orderCount > 0): ?>
    <?php foreach ($orders as $index => $order): ?>
        <?php
            // Decode each order's _transaction JSON data
            $transactionData = json_decode($order['_transaction'], true);

            // Set order details from the transaction data
            $orderNumber = $index + 1;
            $status = $transactionData['status'] ?? 'Pending'; // Status from JSON data
            $price = isset($transactionData['total_amount']) ? (float) $transactionData['total_amount'] : 0.00;
            $date = !empty($transactionData['order_date']) ? date('j F, Y', strtotime($transactionData['order_date'])) : 'N/A';
        ?>

        <div class="order-card p-4 bg-white shadow-md rounded-md flex justify-between items-center mb-4">
              <div class="order-details flex items-center">
                  <img class="icon-order" src="active.png" alt="Order Icon" class="mr-3" />
                  <div>
                      <h3 class="text-md font-bold">Order No: <?= htmlspecialchars($orderNumber); ?></h3>
                      <p  class="stat" id="order-status-<?= htmlspecialchars($order['id']); ?>" class="text-blue-500">Status: <?= htmlspecialchars($status); ?></p>
                  </div>
              </div>
              <div class="order-price-date text-right">
                  <p class="text-lg font-bold">₱<?= htmlspecialchars(number_format($price, 2)); ?></p>
                  <p class="text-gray-600"><?= htmlspecialchars($date); ?></p>
              </div>
          </div>

    <?php endforeach; ?>
<?php else: ?>
    <p class="no-orders">No orders found.</p>
<?php endif; ?>


    </div>

    <!-- Bottom Navigation -->
    <div class="bottom-nav">
        <a href="userdashboard.php" class="nav-item">
            <img src="home.png" alt="Home">
        </a>
        <a href="packages.php" class="nav-item">
            <img src="package.png" alt="Package">
                </a>
        <a href="profile.php" class="nav-item">
            <img src="profile.png" alt="Profile">
        </a>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

    <script> // Initialize Swiper
new Swiper('.card-wrapper', {
  loop: true,
  spaceBetween: 30,
  pagination: {
    el: '.swiper-pagination',
    clickable: true,
    dynamicBullets: true
  },
 
  breakpoints: {
    0: {
      slidesPerView: 1
    },
    768: {
      slidesPerView: 2
    },
    1024: {
      slidesPerView: 3
    }
  }
});


function viewOrderDetails(orderData, promoId) {
    // Create an object with the specific order details we need to store
    const selectedOrder = {
        promoNumber: orderData.promo_number,
        status: orderData.status,
        date: orderData.order_date,
        price: parseFloat(orderData.total_amount).toFixed(2) // Format price to two decimal places
    };

    // Save the order details to localStorage as a JSON string
    localStorage.setItem("selectedOrder", JSON.stringify(selectedOrder));

    // Redirect to the order details page with the promo ID in the query string
    window.location.href = "orderlist.php?promo_id=" + promoId;
}

// Function to fetch and update order statuses
function fetchOrderStatuses() {
    var xhr = new XMLHttpRequest();
    xhr.open("GET", "fetch_status.php", true);
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4 && xhr.status === 200) {
            var response = JSON.parse(xhr.responseText);
            if (response.status === 'success') {
                response.orders.forEach(function(order) {
                    // Find the corresponding order element by ID and update its status
                    var orderElement = document.querySelector("#order-status-" + order.id);
                    if (orderElement) {
                        orderElement.textContent = "Status: " + order.status;
                    }
                });
            }
        }
    };
    xhr.send();
}

// Poll the server every 5 seconds to check for updates
setInterval(fetchOrderStatuses, 2000);

</script>

    <script src="/js/userDash.js"></script>
</body>
</html>
