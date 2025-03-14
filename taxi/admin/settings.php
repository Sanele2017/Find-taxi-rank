<?php
// Database configuration
$host = 'localhost';
$dbname = 'taxi-rank';
$username = 'root';
$password = '';

try {
    // Establish a database connection
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Fetch all users
$query = $pdo->query("SELECT * FROM admins");
$users = $query->fetchAll(PDO::FETCH_ASSOC);

// Handle user deletion
if (isset($_POST['delete'])) {
    $id = $_POST['user_id'];
    $stmt = $pdo->prepare("DELETE FROM admins WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: settings.php");
    exit;
}

// Handle password reset
if (isset($_POST['reset_password'])) {
    $id = $_POST['user_id'];
    $new_password = $_POST['new_password'];

    // Validate password strength
    if (strlen($new_password) < 8 || 
        !preg_match('/[A-Z]/', $new_password) || 
        !preg_match('/[a-z]/', $new_password) || 
        !preg_match('/[0-9]/', $new_password) || 
        !preg_match('/[\W_]/', $new_password)) {
        $error = "Password must be at least 8 characters long and include an uppercase letter, a lowercase letter, a number, and a special character.";
    } else {
        // Hash the password
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        // Update the password in the database
        $stmt = $pdo->prepare("UPDATE admins SET password = ? WHERE id = ?");
        $stmt->execute([$hashed_password, $id]);

        $success = "Password reset successfully.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Settings</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="admin-dashboard.css">
</head>
<style>
/* Main Content */
.main-content {
    margin-left: 18rem;
    padding: 20px;
}

.main-content h1 {
    font-weight: 600;
    color: #212529;
}

/* Table Styling */
.table {
    margin-top: 1.5rem;
    border: 1px solid #dee2e6;
    border-radius: 10px;
    overflow: hidden;
}

.table thead {
    background-color: blue;
    color: #fff;
    text-align: center;
}

.table tbody tr {
    text-align: center;
    transition: background-color 0.3s ease;
}

.table tbody tr:hover {
    background-color: #f1f1f1;
}

.table input[type="password"] {
    border-radius: 20px;
    padding: 5px 10px;
    border: 1px solid #ced4da;
}

/* Alerts */
.alert {
    border-radius: 10px;
    font-weight: 500;
    animation: fadeIn 1s;
}

#success-message {
    background-color: #28a745;
    color: #fff;
}

.alert-danger {
    background-color: #dc3545;
    color: #fff;
}

/* Button Styling */
.btn {
    border-radius: 20px;
    font-size: 0.9rem;
    padding: 5px 15px;
}

.btn-danger {
    background-color: #e63946;
    border: none;
}

.btn-danger:hover {
    background-color: #c82e3d;
}

.btn-warning {
    background-color: #ffb703;
    border: none;
    color: #212529;
}

.btn-warning:hover {
    background-color: #f4a602;
}

/* Animations */
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

</style>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-3 col-lg-2 d-md-block sidebar">
                <h3 class="mb-4">Admin Panel</h3>
                <a href="admin-dashboard.php" class="active">Dashboard</a>
                <a href="#" data-bs-toggle="collapse" data-bs-target="#manageRanksSubmenu" aria-expanded="false" aria-controls="manageRanksSubmenu">Manage Ranks</a>
                <div class="collapse" id="manageRanksSubmenu">
                    <div class="submenu">
                        <a href="view-rank.php">View Ranks</a>
                        <a href="add-ranks.php">Add Rank</a>
                        <a href="route.php">Add Routes</a>
                    </div>
                </div>
                <a href="settings.php">Settings</a>
                <a href="login.php">Logout</a>
            </nav>

             <!-- Main Content -->
             <main class="col-md-9 col-lg-10 main-content">
                <h1>Settings</h1>

                <!-- Manage User Accounts -->
                <section class="mt-4">
                <h3>Manage User Accounts</h3>

                <!-- Display success or error messages -->
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php elseif (!empty($success)): ?>
                    <div class="alert alert-success" id="success-message"><?= htmlspecialchars($success) ?></div>
                <?php endif; ?>

                <table class="table">
                    <thead>
                        <tr>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?= htmlspecialchars($user['username']) ?></td>
                                <td><?= htmlspecialchars($user['email']) ?></td>
                                <td>
                                    <!-- Delete User -->
                                    <form method="POST" class="d-inline">
                                        <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                        <button type="submit" name="delete" class="btn btn-danger btn-sm">Delete</button>
                                    </form>

                                    <!-- Reset Password -->
                                    <form method="POST" class="d-inline">
                                        <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                        <input type="password" name="new_password" placeholder="New Password" class="form-control form-control-sm d-inline" style="width: 150px;">
                                        <button type="submit" name="reset_password" class="btn btn-warning btn-sm">Reset Password</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </section>
            </main>
        </div>
    </div>
</main>
</div>
</div>

    <!-- Bootstrap JavaScript Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
     <!-- Bootstrap and Popper.js -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.min.js"></script>
<!-- JavaScript to Hide Success Message -->
<script>
    // Check if the success message exists
    const successMessage = document.getElementById('success-message');
    if (successMessage) {
        setTimeout(() => {
            successMessage.style.display = 'none';
        }, 2000); // 2000ms = 2 seconds
    }
</script>

</body>
</html>
