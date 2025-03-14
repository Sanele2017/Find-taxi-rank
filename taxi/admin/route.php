<?php
// Database connection parameters
$host = 'localhost';
$db = 'taxi-rank';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    // Establishing the database connection
    $pdo = new PDO($dsn, $user, $pass, $options);

    // Fetching ranks from the database
    $stmt = $pdo->query("SELECT ranks_id as id, rank_name as name FROM ranks");
    $ranks = $stmt->fetchAll();

    // Handling POST request for adding routes and fares
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $rank_id = $_POST['rankSelect']; // Get the rank_id from the POST data

        // Loop through the dynamically added routes and fares
        foreach ($_POST as $key => $value) {
            if (strpos($key, 'route_') !== false) {
                $route_number = str_replace('route_', '', $key);
                $route_name = $value;
                $fare_key = 'fare_' . $route_number;
                $fare = $_POST[$fare_key];

                // Insert route and fare into the database
                $stmt = $pdo->prepare("INSERT INTO routes (ranks_id, route_name, fare) VALUES (?, ?, ?)");
                $stmt->execute([$rank_id, $route_name, $fare]);
            }
        }

        // Send success response
        echo "<script>alert('Routes and fares saved successfully!');</script>";
    }

} catch (\PDOException $e) {
    // Handle any errors during database operations
    echo "<script>alert('Error: " . $e->getMessage() . "');</script>";
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Add Rank</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="admin-dashboard.css">
    <style>
        /* Styling for form labels, inputs, buttons, and success message */
        .form-label {
            font-weight: 600;
            color: #4f4f4f;
        }
        .form-control {
            border-radius: 8px;
            padding: 12px;
            border: 1px solid #ddd;
            margin-bottom: 15px;
            transition: all 0.3s ease;
        }
        .form-control:focus {
            border-color: #3f9cb5;
            box-shadow: 0 0 5px rgba(63, 156, 181, 0.3);
        }
        .btn-primary {
            background: linear-gradient(45deg, #3f9cb5, #5cdb95);
            border: none;
            padding: 12px;
            border-radius: 8px;
            color: #fff;
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            background: linear-gradient(45deg, #5cdb95, #3f9cb5);
            cursor: pointer;
        }
        .card-body {
            padding: 30px;
            background-color: #fafafa;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
        }
        #successMessage {
            padding: 15px;
            background-color: #28a745;
            color: #fff;
            border-radius: 8px;
            margin-top: 20px;
            display: none;
            animation: fadeIn 1s ease-in-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        .mt-4 {
            margin-top: 30px;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar Navigation -->
            <nav class="col-md-3 col-lg-2 d-md-block sidebar">
                <h3 class="mb-4">Admin Panel</h3>
                <a href="admin-dashboard.php" class="active">Dashboard</a>
                <a href="#" data-bs-toggle="collapse" data-bs-target="#manageRanksSubmenu" aria-expanded="false" aria-controls="manageRanksSubmenu">Manage Ranks</a>
                <div class="collapse" id="manageRanksSubmenu">
                    <div class="submenu">
                        <a href="view-rank.php">View Ranks</a>
                        <a href="add-rank.php">Add Rank</a>
                        <a href="route.php">Add Routes</a>
                    </div>
                </div>
                <a href="settings.php">Settings</a>
                <a href="login.php">Logout</a>
            </nav>

            <!-- Main Content -->
            <main class="col-md-9 col-lg-10 main-content">
                <h1 class="mb-4">Add Rank</h1>
                <p class="text-muted">Fill in the form to add a new rank's routes and fares.</p>
                <div class="row mt-4">
                    <div class="col-md-8">
                        <!-- Dropdown for Selecting Rank -->
                        <form method="POST">
                            <div class="mb-3">
                                <label for="rankSelect" class="form-label">Select Rank</label>
                                <select class="form-control" id="rankSelect" name="rankSelect" required>
                                    <option value="">Choose a rank</option>
                                    <?php foreach ($ranks as $rank): ?>
                                        <option value="<?= $rank['id']; ?>"><?= htmlspecialchars($rank['name']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Routes and Fares Section -->
                            <div class="row mt-4">
                                <div class="col-md-8">
                                    <h3 class="mb-3">Add Routes and Fares</h3>

                                    <!-- Manually Add Routes and Fares -->
                                    <div class="card-body">
                                        <h5>Add Routes and Fares Manually</h5>
                                        <div id="routesList">
                                            <!-- Rows for input fields will be added here -->
                                        </div>
                                        <button type="button" class="btn btn-success" id="addRowBtn">+ Add Row</button>
                                        <button type="submit" class="btn btn-primary w-100 mt-3">Submit Routes & Fares</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Bootstrap JS and custom JavaScript for adding routes -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Dynamically add rows for route and fare
        let rowCount = 0;
        document.getElementById('addRowBtn').addEventListener('click', function () {
            rowCount++;
            const routesList = document.getElementById('routesList');

            // Create a new row
            const newRow = document.createElement('div');
            newRow.classList.add('row', 'mb-3');
            newRow.innerHTML = `
                <div class="col-md-5">
                    <input type="text" class="form-control" name="route_${rowCount}" placeholder="Enter route (e.g., A to B)" required>
                </div>
                <div class="col-md-5">
                    <input type="number" class="form-control" name="fare_${rowCount}" placeholder="Enter fare (e.g., 20)" required>
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-danger removeRowBtn">Remove</button>
                </div>
            `;
            routesList.appendChild(newRow);
        });

        // Event delegation for removing rows
        document.getElementById('routesList').addEventListener('click', function (e) {
            if (e.target.classList.contains('removeRowBtn')) {
                e.target.closest('.row').remove();
            }
        });
    </script>
</body>
</html>
