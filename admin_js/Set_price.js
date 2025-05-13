let products = [];
let editIndex = -1;

function addProduct() {
  const productName = document.getElementById('productName').value;
  const packageName = document.getElementById('packageName').value;
  const category = document.getElementById('category').value;
  const productImage = document.getElementById('productImage').files[0];
  const price = document.getElementById('price').value;

  if (productName && packageName && category && productImage && price) {
    // Create a product object
    const newProduct = {
      productName,
      packageName,
      category,
      price,
      productImage: URL.createObjectURL(productImage) // Create a URL for the uploaded image
    };

    if (editIndex === -1) {
      products.push(newProduct); // Add new product
    } else {
      products[editIndex] = newProduct; // Update existing product
      editIndex = -1; // Reset edit index
      document.getElementById('updateProductBtn').style.display = 'none';
    }

    document.getElementById("addProductForm").reset(); // Clear the form
    displayProducts(); // Update the displayed products
    alert("Product Added Successfully!");
  } else {
    alert("Please fill in all fields.");
  }
}

function displayProducts() {
  const productTableBody = document.getElementById('productTableBody');
  productTableBody.innerHTML = ''; // Clear existing products

  products.forEach((product, index) => {
    const newRow = document.createElement("tr");
    
    newRow.innerHTML = `
      <td>${product.productName}</td>
      <td>${product.packageName}</td>
      <td>${product.category}</td>
      <td>${product.price}</td>
      <td>
        <button onclick="editProduct(${index})">Edit</button>
        <button onclick="deleteProduct(${index})">Delete</button>
      </td>
    `;

    productTableBody.appendChild(newRow); // Add new row to the table
  });
}

function editProduct(index) {
  const product = products[index];
  document.getElementById('productName').value = product.productName;
  document.getElementById('packageName').value = product.packageName;
  document.getElementById('category').value = product.category;
  document.getElementById('price').value = product.price;

  editIndex = index; // Set the current index for editing
  document.getElementById('updateProductBtn').style.display = 'inline-block'; // Show update button
}

function deleteProduct(index) {
  products.splice(index, 1); // Remove product from the array
  displayProducts(); // Update the displayed products
}
