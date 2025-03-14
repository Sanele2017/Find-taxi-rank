// Select the form and inputs
const form = document.getElementById("create-account-form");
const passwordInput = document.getElementById("password");
const emailInput = document.getElementById("email");
const usernameInput = document.getElementById("username");
const passwordHelpText = document.querySelector(".text-muted");
const togglePasswordButton = document.querySelector(".toggle-password");
const toggleIcon = document.getElementById("toggle-icon");

// Helper function to check email validity
function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

// Helper function to check password strength
function isStrongPassword(password) {
    const strongPasswordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;
    return strongPasswordRegex.test(password);
}

// Real-time password validation
passwordInput.addEventListener("input", () => {
    const password = passwordInput.value;

    if (isStrongPassword(password)) {
        passwordInput.classList.remove("is-invalid");
        passwordInput.classList.add("is-valid");
        passwordHelpText.style.color = "green";
        passwordHelpText.textContent = "Strong password!";
    } else {
        passwordInput.classList.remove("is-valid");
        passwordInput.classList.add("is-invalid");
        passwordHelpText.style.color = "red";
        passwordHelpText.textContent =
            "Password must be at least 8 characters long, include one uppercase letter, one lowercase letter, one number, and one special character.";
    }
});

// Toggle password visibility
togglePasswordButton.addEventListener("click", () => {
    if (passwordInput.type === "password") {
        passwordInput.type = "text";
        toggleIcon.classList.remove("fa-eye");
        toggleIcon.classList.add("fa-eye-slash");
    } else {
        passwordInput.type = "password";
        toggleIcon.classList.remove("fa-eye-slash");
        toggleIcon.classList.add("fa-eye");
    }
});

// Form submission validation
form.addEventListener("submit", (e) => {
    e.preventDefault(); // Prevent the default form submission

    const email = emailInput.value.trim();
    const password = passwordInput.value;
    const username = usernameInput.value.trim();

    // Email validation
    if (!isValidEmail(email)) {
        emailInput.classList.add("is-invalid");
        emailInput.focus();
        return;
    } else {
        emailInput.classList.remove("is-invalid");
        emailInput.classList.add("is-valid");
    }

    // Password validation
    if (!isStrongPassword(password)) {
        passwordInput.focus();
        return;
    }

    // Prepare data to be sent to PHP
    const formData = new FormData();
    formData.append("username", username);
    formData.append("email", email);
    formData.append("password", password);

    // Use fetch API to send data to PHP
    fetch("create-account.php", {
        method: "POST",
        body: formData,
    })
        .then((response) => response.text())
        .then((data) => {
            alert(data); // Provide user feedback
            if (data.includes("Account created successfully!")) {
                window.location.href = "login.php"; // Redirect on success
            }
        })
        .catch((error) => {
            console.error("Error:", error);
            alert("An error occurred. Please try again.");
        });
});
