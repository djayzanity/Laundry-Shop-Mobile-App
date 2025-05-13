let quantity = 1;
const footer = document.querySelector("footer");
const itemNameDisplay = document.getElementById("item-name");
const itemPriceDisplay = document.getElementById("item-price");
const quantityDisplay = document.getElementById("quantity");
const nextButton = document.getElementById("next-btn");

function increment() {
    quantity++;
    quantityDisplay.innerText = quantity;
    toggleFooterVisibility();
}

function decrement() {
    if (quantity > 1) {
        quantity--;
        quantityDisplay.innerText = quantity;
        toggleFooterVisibility();
    }
}

function toggleFooterVisibility() {
    if (quantity > 0) {
        footer.style.display = "flex";
    } else {
        footer.style.display = "none";
        itemNameDisplay.innerText = "";
        itemPriceDisplay.innerText = "";
    }
}

function addToCart(productId, productName, productPrice) {
    quantity = 1;
    quantityDisplay.innerText = quantity;

    fetch("ordercart.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded",
        },
        body: new URLSearchParams({
            product_id: productId,
            quantity: quantity,
        })
    })
    .then(response => response.text())
    .then(message => {
        alert(message);
        displayCartItem(productName, productPrice);
        nextButton.disabled = false;
        const button = document.querySelector(`button[data-product-id="${productId}"]`);
        button.innerText = "Modify Service";
        toggleFooterVisibility();
    })
    .catch(error => {
        console.error("Error:", error);
        alert("An error occurred while adding the item to the cart.");
    });
}

function displayCartItem(productName, productPrice) {
    itemNameDisplay.innerText = productName;
    itemPriceDisplay.innerText = `$${productPrice}`;
}

function goToReviewOrder() {
    window.location.href = "revieworder.php";
}

function showSection(section) {
    document.querySelectorAll(".tab").forEach(tab => tab.classList.remove("active"));
    document.querySelector(`.tab[data-section="${section}"]`).classList.add("active");

    document.querySelectorAll(".items").forEach(element => element.classList.remove("active"));
    document.getElementById(section).classList.add("active");
}

document.querySelectorAll(".add-to-cart").forEach(button => {
    button.addEventListener("click", () => {
        const productId = button.getAttribute("data-product-id");
        const productName = button.getAttribute("data-product-name");
        const productPrice = button.getAttribute("data-product-price");

        addToCart(productId, productName, productPrice);
    });
});

// Initial state: hide the footer if no items are in cart
toggleFooterVisibility();