<?php
// Start the session
session_start();

// Check if the user is logged in (check for 'admin_id' session key)
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php"); // Redirect to login if not logged in
    exit;
}

// Database connection
$servername = "localhost";
$username = "root";
$password = ""; // Replace with your database password
$dbname = "taxi-rank"; // Replace with your database name

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch admin details
$admin_id = $_SESSION['admin_id']; // Use the correct session key
$sql = "SELECT username FROM admins WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $admin = $result->fetch_assoc();
    $username = $admin['username'];
} else {
    $username = "Admin"; // Default if not found
}

// Fetch total ranks
$totalRanksQuery = "SELECT COUNT(*) AS total_ranks FROM ranks";
$result = $conn->query($totalRanksQuery);
$totalRanks = $result->fetch_assoc()['total_ranks'];

// Fetch ranks per city
$ranksPerCityQuery = "SELECT city, COUNT(*) AS total FROM ranks GROUP BY city";
$ranksPerCityResult = $conn->query($ranksPerCityQuery);

$citiesQuery = "SELECT DISTINCT city FROM ranks";
$citiesResult = $conn->query($citiesQuery);


$routeFaresQuery = "SELECT route_name, fare FROM routes";
$routeFaresResult = $conn->query($routeFaresQuery);

$routeNames = [];
$routeFares = [];

while ($row = $routeFaresResult->fetch_assoc()) {
    $routeNames[] = $row['route_name'];
    $routeFares[] = $row['fare'];
}


if (isset($_POST['export_csv'])) {
    $exportQuery = "SELECT * FROM ranks";
    $exportResult = $conn->query($exportQuery);

    header('Content-Type: text/csv');
    header('Content-Disposition: attachment;filename=ranks_data.csv');

    $output = fopen("php://output", "w");
    fputcsv($output, ['Ranks ID', 'City', 'Rank Name', 'Location', 'Association', 'Operating Hours']);

    while ($row = $exportResult->fetch_assoc()) {
        fputcsv($output, $row);
    }

    fclose($output);
    exit;
}

$city = isset($_POST['city']) ? $_POST['city'] : '';
$start_date = isset($_POST['start_date']) ? $_POST['start_date'] : '';
$end_date = isset($_POST['end_date']) ? $_POST['end_date'] : '';

// Query to fetch newly added routes and fares
$sql = "SELECT * FROM routes WHERE 1";

if ($city) {
    $sql .= " AND city = '" . $city . "'";
}
if ($start_date && $end_date) {
    $sql .= " AND created_at BETWEEN '" . $start_date . "' AND '" . $end_date . "'";
}

$result = $conn->query($sql);
$stmt->close();
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="admin-dashboard.css">
</head>
<style>
    /* Main Container Styling */
.main-content {
    background: #f9f9f9; /* Light background to contrast */
    padding: 2rem;
    border-radius: 10px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

/* Main Title */
.main-content h1 {
    font-size: 2.5rem;
    font-weight: bold;
    color: #2d3436;
    margin-bottom: 1rem;
    text-align: center;
    position: relative;
}

.main-content h1::after {
    content: '';
    width: 50px;
    height: 3px;
    background: black;
    display: block;
    margin: 0.5rem auto 0;
    border-radius: 2px;
}

/* Subtitle */
.main-content .text-muted {
    font-size: 1.1rem;
    color: black;
    text-align: center;
    margin-bottom: 2rem;
}

/* Section Headings */
.main-content h3 {
    font-size: 1.8rem;
    color: black;
    font-weight: bold;
    margin-bottom: 1rem;
    position: relative;
}

.main-content h3::before {
    content: '';
    width: 5px;
    height: 30px;
    background: black;
    position: absolute;
    top: 5px;
    left: -15px;
    border-radius: 3px;
}

/* Card Styling */
.card {
    border: none;
    border-radius: 10px;
    box-shadow: 0 3px 6px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 12px rgba(0, 0, 0, 0.15);
}

.card-body {
    background: #ffffff;
    border-radius: 10px;
    padding: 1.5rem;
}

.card-title {
    font-size: 1.5rem;
    font-weight: bold;
    color: #0984e3;
    margin-bottom: 0.5rem;
}

.card-text {
    font-size: 1.1rem;
    color: #636e72;
}

/* List Styling */
.list-unstyled li {
    font-size: 1rem;
    color: #2d3436;
    padding: 0.3rem 0;
}

/* Chart Containers */
canvas {
    background: #ffffff;
    border: 1px solid #dfe6e9;
    border-radius: 10px;
    padding: 1rem;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

/* Buttons */
.btn-secondary {
    background: #6c5ce7;
    color: #ffffff;
    border: none;
    padding: 0.8rem 1.5rem;
    border-radius: 50px;
    font-size: 1rem;
    font-weight: bold;
    transition: background-color 0.3s ease;
}

.btn-secondary:hover {
    background: #341f97;
    color: #ffffff;
}

/* Hover Pulse Animation */
@keyframes pulse {
    0% {
        transform: scale(1);
        box-shadow: 0 3px 6px rgba(0, 0, 0, 0.1);
    }
    50% {
        transform: scale(1.05);
        box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
    }
    100% {
        transform: scale(1);
        box-shadow: 0 3px 6px rgba(0, 0, 0, 0.1);
    }
}

/* Cards Animation */
.card:hover {
    animation: pulse 0.6s ease-in-out;
}

/* Button Pulse Animation */
.btn-secondary {
    position: relative;
    overflow: hidden;
}

.btn-secondary::after {
    content: '';
    position: absolute;
    width: 200%;
    height: 200%;
    top: -100%;
    left: -100%;
    background: radial-gradient(circle, rgba(255, 255, 255, 0.6), rgba(255, 255, 255, 0));
    transform: scale(0);
    transition: transform 0.5s ease;
}

.btn-secondary:hover::after {
    transform: scale(1);
}

/* Icon Animation */
.card-body h5::before {
    content: '\f201'; /* FontAwesome icon (e.g., a chart) */
    font-family: 'FontAwesome';
    color: #16a085;
    margin-right: 0.5rem;
    animation: bounce 1.2s infinite;
}

@keyframes bounce {
    0%, 100% {
        transform: translateY(0);
    }
    50% {
        transform: translateY(-5px);
    }
}

/* Smooth Fade-In on Page Load */
.main-content {
    animation: fadeIn 1s ease-in-out;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(10px);
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
                <h1>Welcome, <?php echo htmlspecialchars($username); ?>!</h1>
                <p class="text-muted">Hello <?php echo htmlspecialchars($username); ?>. Here, you can manage ranks, view reports, and configure settings.</p>
                <!-- View Analytics -->
                <section class="mb-5">
                    <h3><i class="fas fa-chart-line"></i> View Analytics</h3>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <h5><i class="fas fa-chart-pie"></i> Ranks Overview</h5>
                                    <p class="card-text">Total Ranks: <?php echo $totalRanks; ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <h5><i class="fas fa-city"></i> Ranks Per City</h5>
                                    <ul class="list-unstyled">
                                        <?php while ($row = $ranksPerCityResult->fetch_assoc()) { ?>
                                            <li><i class="fas fa-map-marker-alt"></i> <?php echo $row['city']; ?>: <?php echo $row['total']; ?></li>
                                        <?php } ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                
                <!-- Visualize Data -->
                <section class="mb-5">
                    <h3>Visualize Data</h3>
                    <div class="row">
                        <div class="col-md-4">
                            <canvas id="barChart"></canvas>
                        </div>
                        <div class="col-md-4">
                            <canvas id="pieChart"></canvas>
                        </div>
                        <div class="col-md-4">
                            <canvas id="lineChart"></canvas>
                        </div>
                    </div>
                </section>

                <!-- Export Data -->
                <section class="mb-5">
                    <h3>Export Data</h3>
                    <p>Download reports for offline use.</p>
                    <form method="POST">
                    <button type="submit" name="export_csv" class="btn btn-secondary">Export as CSV</button>
                </form>
                </section>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Include Chart.js for Data Visualization -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <canvas id="barChart"></canvas>
    <script>
        const ctx = document.getElementById('barChart').getContext('2d');
    const barChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($routeNames); ?>,
            datasets: [{
                label: 'Fare (in currency)',
                data: <?php echo json_encode($routeFares); ?>,
                backgroundColor: 'rgba(54, 162, 235, 0.6)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: { beginAtZero: true }
            }
        }
    });


        document.getElementById('generateReportBtn').addEventListener('click', function () {
            // Get the form values
            const city = document.querySelector('[name="city"]').value;
            const startDate = document.querySelector('[name="start_date"]').value;
            const endDate = document.querySelector('[name="end_date"]').value;

            // Send data to the server using AJAX to fetch newly added routes and fares
            $.ajax({
                type: "POST",
                url: "generate_report.php", // PHP file to fetch the data
                data: {
                    city: city,
                    start_date: startDate,
                    end_date: endDate
                },
                success: function(response) {
                    // Display the report data inside the modal
                    document.getElementById('modalReportData').innerHTML = response;

                    // Open the modal
                    const modal = new bootstrap.Modal(document.getElementById('reportModal'));
                    modal.show();
                },
                error: function() {
                    document.getElementById('modalReportData').innerText = "Failed to load data.";
                }
            });
        });
    </script>
</body>
</html>
