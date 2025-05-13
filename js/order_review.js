document.addEventListener('DOMContentLoaded', function () {
    const orderItems = document.querySelectorAll('.order-item');
    const subtotalDisplay = document.querySelector('.subtotal span');
    const serviceFeeDisplay = document.querySelector('.service-fee span');
    const paymentAmountDisplay = document.querySelector('.payment-amount span');

    let serviceFee = parseFloat(serviceFeeDisplay.textContent.replace('₱', ''));
    
    const updateTotalAmount = () => {
        let subtotal = 0;
        orderItems.forEach(item => {
            const priceDisplay = item.querySelector('.price');
            subtotal += parseFloat(priceDisplay.textContent.replace('₱', ''));
        });
        subtotalDisplay.textContent = '₱' + subtotal.toFixed(2);
        paymentAmountDisplay.textContent = '₱' + (subtotal + serviceFee).toFixed(2);
    };

    orderItems.forEach(item => {
        const addButton = item.querySelector('.add');
        const minusButton = item.querySelector('.minus');
        const removeButton = item.querySelector('.remove');
        const qtyDisplay = item.querySelector('.qty');
        const priceDisplay = item.querySelector('.price');

        let qty = parseInt(qtyDisplay.textContent);
        const pricePerUnit = parseFloat(priceDisplay.textContent.replace('₱', '')) / qty; // Adjust price per unit

        const updatePrice = () => {
            priceDisplay.textContent = '₱' + (qty * pricePerUnit).toFixed(2);
            updateTotalAmount();
        };

        addButton.addEventListener('click', function () {
            qty++;
            qtyDisplay.textContent = qty;
            updatePrice();
        });

        minusButton.addEventListener('click', function () {
            if (qty > 1) {
                qty--;
                qtyDisplay.textContent = qty;
                updatePrice();
            }
        });

        removeButton.addEventListener('click', function () {
            item.remove();
            updateTotalAmount();
        });
    });

    const confirmButton = document.querySelector('.confirm-btn');
    confirmButton.addEventListener('click', function () {
        alert('Order confirmed!');
        // Logic for order confirmation can be implemented here
    });
});
