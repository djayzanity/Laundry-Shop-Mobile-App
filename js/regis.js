// Select the checkbox and submit button
const checkbox = document.getElementById('accept');
const submitBtn = document.getElementById('submit-btn');

// Add event listener to the checkbox
checkbox.addEventListener('change', function() {
    // Enable the submit button if the checkbox is checked
    submitBtn.disabled = !checkbox.checked;
});
