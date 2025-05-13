<?php
// Include your database connection file
include '../connection.php'; // Adjust the path if necessary

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customer_id = 1; // Set this to the appropriate customer ID
    $service_name = $_POST['productName'];
    $package_name = $_POST['packageName'];
    $service_price = $_POST['price'];
    
    // Handle file upload
    $image_url = 'clothes/' . basename($_FILES['productImage']['name']);
    move_uploaded_file($_FILES['productImage']['tmp_name'], $image_url);

    // Prepare and bind
    $stmt = $pdo->prepare("INSERT INTO items (customer_id, service_name, service_price, package_name, package_price, image_url) VALUES (?, ?, ?, ?, ?, ?)");
    $package_price = 0; // Assuming package price is 0 for now; adjust as needed
    $stmt->execute([$customer_id, $service_name, $service_price, $package_name, $package_price, $image_url]);

    if ($stmt) {
        echo "<script>alert('Product added successfully!');</script>";
    } else {
        echo "<script>alert('Error adding product.');</script>";
    }
}

// Fetch and display all products
$products = $pdo->query("SELECT * FROM items")->fetchAll(PDO::FETCH_ASSOC);

// Fetch categories from the database
$stmt = $pdo->prepare("SELECT * FROM categories");
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Add New Product</title>
  <link rel="stylesheet" href="/admin_css/set_price.css">
</head>
<body>
  <div class="form-container">
    <h2>Add New Product</h2>

    <!-- Display Table -->
    <table id="productTable">
      <thead>
        <tr>
          <th>Product</th>
          <th>Package</th>
          <th>Image</th>
          <th>Price</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody id="productTableBody">
        <?php foreach ($products as $product): ?>
          <tr>
            <td><?= htmlspecialchars($product['service_name']) ?></td>
            <td><?= htmlspecialchars($product['package_name']) ?></td>
            <td><img src="<?= htmlspecialchars($product['image_url']) ?>" alt="<?= htmlspecialchars($product['service_name']) ?>" width="50"></td>
            <td><?= htmlspecialchars($product['service_price']) ?></td>
            <td>
              <button onclick="editProduct(<?= $product['id'] ?>)">Edit</button>
              <button onclick="deleteProduct(<?= $product['id'] ?>)">Delete</button>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>

    <form id="addProductForm" enctype="multipart/form-data" action="set_price.php" method="POST">
      <table>
        <tr>
          <td><label for="productName">Product:</label></td>
          <td><input type="text" name="productName" id="productName" placeholder="Enter product name" required></td>
        </tr>
        <tr>
          <td><label for="packageName">Package:</label></td>
          <td><input type="text" name="packageName" id="packageName" placeholder="Enter package name" required></td>
        </tr>
        <tr>
        <td><label for="category">Category:</label></td>
          <td>
            <select name="category" id="category" required>
              <option value="" disabled selected>Select Category</option>
              <?php foreach ($categories as $category): ?>
                <option value="<?= htmlspecialchars($category['name']) ?>"><?= htmlspecialchars($category['name']) ?></option>
              <?php endforeach; ?>
            </select>
          </td>
        </tr>
        <tr>
          <td><label for="productImage">Upload Icon:</label></td>
          <td><input type="file" name="productImage" id="productImage" accept="image/*" required></td>
        </tr>
        <tr>
          <td><label for="price">Price:</label></td>
          <td><input type="number" name="price" id="price" placeholder="Enter price" required></td>
        </tr>
      </table>
      <button type="submit">Add Product</button>
    </form>
  </div>

  <script src="/admin_js/Set_price.js"></script>
</body>
</html>
