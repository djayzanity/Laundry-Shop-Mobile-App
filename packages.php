<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <!-- <meta name="viewport" content="width=device-width, initial-scale=1.0"> -->
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>Product Cards</title>
    <link rel="stylesheet" href="/css/package.css">
</head>
        <style>
  * {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

body {
  font-family: 'Arial', sans-serif;
  background-color: #fdfdfd;
  color: #333;
  text-align: center;
  transition: opacity 0.5s ease-in-out;
  opacity: 1;
}

body.fade-out {
  opacity: 0;
}

h1 {
  font-size: 42px;
  font-weight: bold;
  margin-bottom: 30px;
  color: #e63946;
}

.product-container {
  display: flex;
  gap: 20px;
  justify-content: center;
  flex-wrap: wrap;
  padding: 20px;
  margin-top: -10px;
}

.product-card {
  background-color: #fff;
  border-radius: 20px;
  padding: 15px;
  margin: 10px;
  height: 580px;
  width: 320px;
  box-shadow: 0 10px 30px rgba(230, 57, 70, 0.15);
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  display: flex;
  flex-direction: column;
  align-items: center;
}

.product-card:hover {
  transform: scale(1.05);
  box-shadow: 0 15px 40px rgba(230, 57, 70, 0.25);
}

.product-image img {
  width: 100%;
  height: auto;
  max-height: 220px;
  object-fit: cover;
  border-radius: 15px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.product-details {
  flex-grow: 1;
  padding: 20px 0;
  text-align: left;
}

h2 {
  font-size: 22px;
  font-weight: bold;
  color: #e63946;
  text-align: center;
  margin-top: 10px;
}

.description {
  color: black;
  font-size: 14px;
  margin-top: 10px;
  padding: 0 15px;
}

.note {
  color: #e63946;
  font-size: 12px;
  padding: 0 15px;
  margin-top: 8px;
}

.price {
  font-size: 26px;
  font-weight: bold;
  color: #e63946;
  margin-top: 15px;
}

button[name="avail_now"] {
  background: linear-gradient(135deg, #e63946, #f78f8f);
  color: #fff;
  font-size: 18px;
  padding: 4px;
  border: none;
  border-radius: 20px;
  position: relative;
  left: -9px;
  width: 118%;
  cursor: pointer;
  transition: background-color 0.3s ease, transform 0.2s ease;
  box-shadow: 0 4px 10px rgba(230, 57, 70, 0.3);
}

button[name="avail_now"]:hover {
  background: linear-gradient(135deg, #d62828, #ff9e9e);
  transform: scale(1.05);
}

button[name="avail_now"]:active {
  background-color: #b51717;
  transform: scale(0.98);
  box-shadow: 0 2px 5px rgba(230, 57, 70, 0.2);
}



.bottom-nav {
  position: relative;
  bottom: 0;
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
}



.nav-item {
  text-align: center;
  flex-grow: 1;
}

.nav-item img {
  width: 22px;
  height: 22px;
}

            </style>
<body>
<h1>Our Packages</h1>
<div class="product-container">
<?php
include 'connection.php';

// Prepare SQL to fetch promo details and associated services
$stmt = $pdo->prepare("
    SELECT 
        p.id, p._name, p.image_url, p._price, p.description, p.points_reward, p.points_cost,
        s._name AS service_name
    FROM promo p
    LEFT JOIN promo_service ps ON p.id = ps.promo_id
    LEFT JOIN _service s ON ps.service_id = s.id
");

$stmt->execute();
$packages = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Initialize an empty array to store the package descriptions
$packageDescriptions = [];

foreach ($packages as $package) {
    $packageId = $package['id'];
    $packageName = $package['_name'];

    // Check if the package already exists in the array, if not, initialize it
    if (!isset($packageDescriptions[$packageId])) {
        $packageDescriptions[$packageId] = [
            'id' => $packageId,
            'name' => $packageName,
            'description' => $package['description'],
            'price' => $package['_price'],
            'image_src' => $package['image_url'],
            'services' => []
        ];
    }

    // Add the service to the services list if it exists
    if (!empty($package['service_name'])) {
        $packageDescriptions[$packageId]['services'][] = $package['service_name'];
    }
}

// Now output the packages dynamically
foreach ($packageDescriptions as $packageData) {
    $packageId = htmlspecialchars($packageData['id']);
    $packageName = htmlspecialchars($packageData['name']);
    $description = htmlspecialchars($packageData['description']);
    $packagePrice = htmlspecialchars($packageData['price']);
    $imageSrc = htmlspecialchars($packageData['image_src']);
    $services = $packageData['services'];
?>
    <div class="product-card">
        <div class="product-image">
            <img src="<?php echo $imageSrc; ?>" alt="<?php echo $packageName; ?>">
        </div>
        <div class="product-details">
            <h2><?php echo $packageName; ?></h2>
            <p class="description"><?php echo $description; ?><br><strong>Included Services:</strong> <?php echo implode(', ', $services);  ?></p>
        </div>
        <div class="price">₱<?php echo $packagePrice; ?></div>

        <form method="post" action="track.php">
            <input type="hidden" name="package_id" value="<?php echo $packageId; ?>">
            <input type="hidden" name="package_name" value="<?php echo $packageName; ?>">
            <input type="hidden" name="package_price" value="<?php echo $packagePrice; ?>">
            <?php foreach ($services as $service): ?>
                <input type="hidden" name="included_services[]" value="<?php echo htmlspecialchars($service); ?>">
            <?php endforeach; ?>
            <button type="submit" name="avail_now">Avail Now</button>
        </form>
    </div>
<?php } ?>
</div>



    </div>

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
    <script src="/js/pack.js"></script>
</body>
</html>