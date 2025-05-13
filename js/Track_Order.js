// Select the circles
const arriveCircle = document.getElementById('arriveCircle');
const deliveryCircle = document.getElementById('deliveryCircle');

// Button to update status
const updateStatusBtn = document.getElementById('updateStatusBtn');

// Variable to track current status
let currentStatus = 0; // 0: Pick up, 1: Arrived, 2: Delivered

// Function to update the status with transitions
updateStatusBtn.addEventListener('click', () => {
    if (currentStatus === 0) {
        // Move to "Arrive at the shop" status
        arriveCircle.classList.add('complete');
        arriveCircle.classList.remove('active');
        currentStatus = 1;
    } else if (currentStatus === 1) {
        // Move to "Delivery Time" status
        deliveryCircle.classList.add('active');
        currentStatus = 2;
    } else if (currentStatus === 2) {
        // Mark as delivered
        deliveryCircle.classList.add('delivered');
        updateStatusBtn.disabled = true; // Disable the button after delivery
        updateStatusBtn.innerText = 'Delivered';
    }
});
