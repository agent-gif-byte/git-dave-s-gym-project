document.addEventListener("DOMContentLoaded", function() {
    const loginForm = document.getElementById("loginForm");
    const pinInput = document.getElementById("pin_input");

    loginForm.addEventListener("submit", function(event) {
        // Quick check to ensure the PIN input isn't empty or too short
        if (pinInput.value.trim().length < 4) {
            alert("Security PIN must be at least 4 digits.");
            event.preventDefault(); // Stop submission
        }
    });
});