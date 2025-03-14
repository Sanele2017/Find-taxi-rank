<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .login-container {
            max-width: 400px;
            margin: 100px auto;
            padding: 30px;
            background: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        .form-footer {
            margin-top: 20px;
            text-align: center;
        }
        .error-message {
            color: red;
            margin-bottom: 10px;
            text-align: center;
        }
        .success-message {
            color: green;
            margin-bottom: 10px;
            text-align: center;
        }
    </style>
</head>
<body>
<?php
session_start(); // Start the session at the top of the page
$errorMessage = "";
$successMessage = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Replace with your actual database connection details
    $conn = new mysqli("localhost", "root", "", "taxi-rank");

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Check credentials
    $sql = "SELECT * FROM admins WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['admin_id'] = $user['id']; // Set the session variable for admin_id
            $successMessage = "Login successful! Redirecting...";
            echo '<meta http-equiv="refresh" content="2;url=admin-dashboard.php">'; // Redirect
        } else {
            $errorMessage = "Invalid password.";
        }
    } else {
        $errorMessage = "Invalid email.";
    }

    $stmt->close();
    $conn->close();
}
?>

    <div class="login-container">
        <h2 class="text-center mb-4">Login</h2>
        <?php if (!empty($errorMessage)): ?>
            <div class="error-message"><?php echo $errorMessage; ?></div>
        <?php endif; ?>
        <?php if (!empty($successMessage)): ?>
            <div class="success-message"><?php echo $successMessage; ?></div>
        <?php endif; ?>
        <form id="login-form" method="POST">
            <!-- Email Field -->
            <div class="mb-3">
                <label for="email" class="form-label">Email Address</label>
                <input
                    type="email"
                    class="form-control"
                    id="email"
                    name="email"
                    placeholder="Enter your email"
                    required
                >
                <div class="invalid-feedback">Please enter a valid email address.</div>
            </div>

            <!-- Password Field -->
            <div class="mb-3 position-relative">
                <label for="password" class="form-label">Password</label>
                <div class="input-group">
                    <input
                        type="password"
                        class="form-control"
                        id="password"
                        name="password"
                        placeholder="Enter your password"
                        required
                    >
                    <button
                        type="button"
                        class="btn btn-outline-secondary toggle-password"
                        tabindex="-1"
                    >
                        <i class="fa fa-eye" id="toggle-icon"></i>
                    </button>
                </div>
                <div class="invalid-feedback">Password must be at least 8 characters long.</div>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn btn-primary w-100">Login</button>

            <!-- Create Account Button -->
            <div class="form-footer">
                <p>Don't have an account?</p>
                <a href="create-account.php" class="btn btn-outline-secondary w-100">Create Account</a>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="login.js"></script>

    <script>
        // JavaScript code
const loginForm = document.getElementById("login-form");
const emailInput = document.getElementById("email");
const passwordInput = document.getElementById("password");
const togglePasswordButton = document.querySelector(".toggle-password");
const toggleIcon = document.getElementById("toggle-icon");

// Helper function to validate email
function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

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

// Form submission
loginForm.addEventListener("submit", (e) => {
    e.preventDefault();

    const email = emailInput.value.trim();
    const password = passwordInput.value;

    let isValid = true;

    // Validate email
    if (!isValidEmail(email)) {
        emailInput.classList.add("is-invalid");
        isValid = false;
    } else {
        emailInput.classList.remove("is-invalid");
        emailInput.classList.add("is-valid");
    }

    // Validate password
    if (password.length < 8) {
        passwordInput.classList.add("is-invalid");
        isValid = false;
    } else {
        passwordInput.classList.remove("is-invalid");
        passwordInput.classList.add("is-valid");
    }

    // If validation passes, send data to PHP
    if (isValid) {
        const formData = new FormData();
        formData.append("email", email);
        formData.append("password", password);

        fetch("login.php", {
            method: "POST",
            body: formData,
        })
            .then((response) => response.text())
            .then((data) => {
                alert(data); // Display server response
                if (data.includes("Login successful")) {
                    window.location.href = "admin-dashboard.php";
                }
            })
            .catch((error) => console.error("Error:", error));
    }
});
    </script>
</body>
</html>