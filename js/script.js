document.addEventListener("DOMContentLoaded", () => {
    const registerForm = document.getElementById("registerForm");

    if (registerForm) {
        registerForm.addEventListener("submit", function (event) {
            let isValid = true;

            // Fullname Validation
            const fullname = document.getElementById("fullname").value.trim();
            if (fullname === "" || !/^[a-zA-Z\s]+$/.test(fullname)) {
                document.getElementById("fullnameError").innerText = "Please enter a valid fullname (letters and spaces only).";
                isValid = false;
            } else {
                document.getElementById("fullnameError").innerText = "";
            }

            // Username Validation
            const username = document.getElementById("username").value.trim();
            if (username.length < 5) {
                document.getElementById("usernameError").innerText = "Username must be at least 5 characters long.";
                isValid = false;
            } else {
                document.getElementById("usernameError").innerText = "";
            }

            
            const email = document.getElementById("email").value.trim();
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                document.getElementById("emailError").innerText = "Please enter a valid email address.";
                isValid = false;
            } else {
                document.getElementById("emailError").innerText = "";
            }

        
            const password = document.getElementById("password").value.trim();
            if (password.length < 8) {
                document.getElementById("passwordError").innerText = "Password must be at least 8 characters long.";
                isValid = false;
            } else {
                document.getElementById("passwordError").innerText = "";
            }

            
            if (!isValid) {
                event.preventDefault();
            }
        });
    }
});

