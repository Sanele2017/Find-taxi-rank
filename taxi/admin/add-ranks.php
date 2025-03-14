<?php

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "taxi-rank";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Insert data into the database
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $city = $_POST['city'];
    $rankName = $_POST['rankName'];
    $location = $_POST['location'];
    $association = $_POST['association'];
    $operatingHours = $_POST['operatingHours'];

    $sql = "INSERT INTO ranks (city, rank_name, location, association, operating_hours)
            VALUES ('$city', '$rankName', '$location', '$association', '$operatingHours')";

    if ($conn->query($sql) === TRUE) {
        echo json_encode(["status" => "success", "message" => "Rank added successfully!"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Error: " . $conn->error]);
    }

    $conn->close();
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
            opacity: 1;
            transition: opacity 1s ease-in-out;
        }

        #successMessage.fade-in {
            opacity: 1;
        }

        #successMessage.d-none {
            display: none;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
    </style>
</head>
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
                <a href="report.php">Reports</a>
                <a href="settings.html">Settings</a>
                <a href="login.php">Logout</a>
            </nav>

            <!-- Main Content -->
            <main class="col-md-9 col-lg-10 main-content">
    <h1 class="mb-4">Add Rank</h1>
    <p class="text-muted">Fill in the form to add a new rank.</p>
    <div class="row mt-4">
        <div class="col-md-8">
            <!-- Add Rank Form -->
            <form id="addRankForm">
                <div class="card-body">
                    <!-- City -->
                    <div class="mb-3">
                        <label for="city" class="form-label">City</label>
                        <input type="text" class="form-control" id="city" required>
                    </div>

                    <!-- Rank Name -->
                    <div class="mb-3">
                        <label for="rankName" class="form-label">Rank Name</label>
                        <input type="text" class="form-control" id="rankName" required>
                    </div>

                    <!-- Location -->
                    <div class="mb-3">
                        <label for="location" class="form-label">Location</label>
                        <input type="text" class="form-control" id="location" required>
                    </div>

                    <!-- Association -->
                    <div class="mb-3">
                        <label for="association" class="form-label">Association</label>
                        <input type="text" class="form-control" id="association" required>
                    </div>

                    <!-- Operating Hours -->
                    <div class="mb-3">
                        <label for="operatingHours" class="form-label">Operating Hours</label>
                        <input type="text" class="form-control" id="operatingHours" required>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-primary w-100">Add Rank</button>
                </div>
            </form>
            <!-- Success Message -->
            <div id="successMessage" class="alert alert-success mt-4 d-none" role="alert">
                Rank added successfully!
            </div>
        </div>
    </div>
</main>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('addRankForm').addEventListener('submit', function (e) {
    e.preventDefault(); // Prevent the form from submitting the traditional way

    // Get form data
    const city = document.getElementById('city').value;
    const rankName = document.getElementById('rankName').value;
    const location = document.getElementById('location').value;
    const association = document.getElementById('association').value;
    const operatingHours = document.getElementById('operatingHours').value;

    // Simple client-side validation (can be expanded if needed)
    if (!city || !rankName || !location || !association || !operatingHours) {
        alert("Please fill out all fields.");
        return;
    }

    // Create a FormData object
    const formData = new FormData();
    formData.append('city', city);
    formData.append('rankName', rankName);
    formData.append('location', location);
    formData.append('association', association);
    formData.append('operatingHours', operatingHours);

    // Send data to PHP backend using Fetch API
    fetch('add-ranks.php', { // replace with actual PHP page name
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            // Show success message
            const successMessage = document.getElementById('successMessage');
            successMessage.classList.remove('d-none');
            
            // Fade out the success message after 3 seconds
            setTimeout(() => {
                successMessage.classList.add('fade-out');
                setTimeout(() => {
                    successMessage.classList.add('d-none');
                    successMessage.classList.remove('fade-out');
                }, 500); // Time for fading out
            }, 3000); // Stay visible for 3 seconds

            // Optionally reset the form
            document.getElementById('addRankForm').reset();
        } else {
            alert('Error: ' + data.message);
        }
    })
});

    </script>
</body>
</html>
