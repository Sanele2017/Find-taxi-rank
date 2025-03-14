<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Admin Account</title>
    <!-- Bootstrap CSS -->
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css"
      rel="stylesheet"
    >
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap"
        rel="stylesheet"
        />
    <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"
    integrity="sha384-k6RqeWeci5ZR/Lv4MR0sA0FfDOMOp0z9sCN7TUtNRF0LiwelEAjjKgtCfjGhNz8L"
    crossorigin="anonymous"
    />
    <link rel="stylesheet" href="styles.css">
</head>
<style>
    /* General Body Styling */
body {
    background: linear-gradient(135deg, #f5f7fa, #c3cfe2);
    font-family: 'Poppins', sans-serif;
}

/* Card Styling */
.card {
    border: none;
    border-radius: 15px;
    background: #ffffff;
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
}

/* Title Styling */
h3 {
    font-weight: bold;
    color: #343a40;
    margin-bottom: 20px;
}

/* Input Fields */
.form-control {
    border-radius: 10px;
    border: 1px solid #ced4da;
    transition: box-shadow 0.3s;
}

.form-control:focus {
    border-color: #28a745;
    box-shadow: 0 0 5px rgba(40, 167, 69, 0.5);
}

/* Password Toggle Button */
.toggle-password {
    border-top-right-radius: 10px;
    border-bottom-right-radius: 10px;
    border: none;
    background: #f8f9fa;
}

.toggle-password i {
    color: #6c757d;
    transition: color 0.3s;
}

.toggle-password:hover i {
    color: #28a745;
}

/* Small Text Styling */
.text-muted {
    font-size: 0.9rem;
}

/* Submit Button */
.btn-success {
    background: #28a745;
    border: none;
    border-radius: 10px;
    transition: background 0.3s ease, transform 0.3s ease;
}

.btn-success:hover {
    background: #218838;
    transform: scale(1.05);
}

/* Hover Effects */
.btn-success:focus, .btn-success:active {
    box-shadow: 0 0 10px rgba(33, 136, 56, 0.6);
}

/* Form Animation */
form {
    animation: fadeIn 1.2s ease-in-out;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
<body>
    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="card shadow p-4" style="width: 400px;">
            <h3 class="text-center mb-4">Create Admin Account</h3>
            <form id="create-account-form" method="POST">
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" class="form-control" id="username" name="username" placeholder="Enter username" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" placeholder="Enter email" required>
                </div>
                <div class="mb-3 position-relative">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-group">
                        <input
                            type="password"
                            class="form-control"
                            id="password"
                            name="password"
                            placeholder="Enter password"
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
                    <small class="text-muted">
                        Password must be at least 8 characters long, include one uppercase letter, one lowercase letter, one number, and one special character.
                    </small>
                </div>                
                <div class="d-grid">
                    <button type="submit" class="btn btn-success">Create Account</button>
                </div>
            </form>
        </div>
    </div>




    <?php
// Database connection
$servername = "localhost";
$username = "root"; // replace with your DB username
$password = ""; // replace with your DB password
$dbname = "taxi-rank"; // replace with your DB name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data from AJAX request
    $admin_username = mysqli_real_escape_string($conn, $_POST['username']);
    $admin_email = mysqli_real_escape_string($conn, $_POST['email']);
    $admin_password = mysqli_real_escape_string($conn, $_POST['password']);

    // Validate email
    if (!filter_var($admin_email, FILTER_VALIDATE_EMAIL)) {
        echo "Invalid email format.";
        exit();
    }

    // Validate password strength
    if (!preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/", $admin_password)) {
        echo "Password must be at least 8 characters long, include one uppercase letter, one lowercase letter, one number, and one special character.";
        exit();
    }

    // Hash the password for security
    $hashed_password = password_hash($admin_password, PASSWORD_BCRYPT);

    // Check if email already exists
    $email_check = "SELECT * FROM admins WHERE email = '$admin_email'";
    $result = $conn->query($email_check);

    if ($result->num_rows > 0) {
        echo "Email already registered!";
        exit();
    }

    // Insert the new admin account into the database
    $sql = "INSERT INTO admins (username, email, password) VALUES ('$admin_username', '$admin_email', '$hashed_password')";
    if ($conn->query($sql) === TRUE) {
        echo "Account created successfully!";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $stmt = $conn->prepare("INSERT INTO admins (username, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $admin_username, $admin_email, $hashed_password);

    if ($stmt->execute()) {
        echo "Account created successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();

    $conn->close();
}

?>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../admin/scripts.js"></script>
    
</body>
</html>
